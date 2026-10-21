(() => {
  'use strict';
  const digits = value => value.replace(/\D/g, '');
  const rules = {
    nome: { max: 100, min: 3, autocomplete: 'name' },
    email: { max: 254, type: 'email', autocomplete: 'email' },
    telefone: { max: 15, mask: 'phone', autocomplete: 'tel-national', hint: 'DDD + telefone: (11) 99999-9999 ou (11) 3333-3333.' },
    cpf: { max: 14, mask: 'cpf', hint: 'Informe os 11 dígitos do CPF.' },
    cep: { max: 9, mask: 'cep', autocomplete: 'postal-code', hint: 'CEP com 8 dígitos: 00000-000.' },
    rua: { max: 120, autocomplete: 'address-line1' },
    bairro: { max: 100 }, cidade: { max: 100, autocomplete: 'address-level2' },
    complemento: { max: 120, autocomplete: 'address-line2' },
    identificacao: { max: 50 }, numero: { max: 6, mask: 'number', hint: 'Número entre 1 e 999999.' },
    descricao: { max: 200 },
    preco: { type: 'number', minValue: '0.01', maxValue: '99999.99', step: '0.01' },
    senha: { min: 8, max: 72, autocomplete: 'new-password', hint: 'Use pelo menos 8 caracteres.' },
    confirmarSenha: { min: 8, max: 72, autocomplete: 'new-password' },
    nova_senha: { min: 8, max: 72, autocomplete: 'new-password' },
    confirmar_nova_senha: { min: 8, max: 72, autocomplete: 'new-password' },
    senha_atual: { max: 256, autocomplete: 'current-password' },
    password: { max: 256, autocomplete: 'current-password' }
  };
  function cpfValid(value) {
    const cpf = digits(value);
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    for (let t = 9; t < 11; t++) {
      let sum = 0;
      for (let i = 0; i < t; i++) sum += Number(cpf[i]) * (t + 1 - i);
      const rest = sum % 11;
      if ((rest < 2 ? 0 : 11 - rest) !== Number(cpf[t])) return false;
    }
    return true;
  }
  function format(value, mask) {
    let v = digits(value).slice(0, { phone: 11, cpf: 11, cep: 8, number: 6 }[mask]);
    if (mask === 'cpf') return v.replace(/^(\d{3})(\d)/, '$1.$2').replace(/^(\d{3}\.\d{3})(\d)/, '$1.$2').replace(/(\d{3}\.\d{3}\.\d{3})(\d)/, '$1-$2');
    if (mask === 'cep') return v.replace(/^(\d{5})(\d)/, '$1-$2');
    if (mask === 'phone') {
      if (v.length <= 2) return v ? '(' + v : '';
      const body = v.slice(2), split = body.length > 8 ? 5 : 4;
      return '(' + v.slice(0, 2) + ') ' + body.slice(0, split) + (body.length > split ? '-' + body.slice(split) : '');
    }
    return v;
  }
  let count = 0;
  function message(input, rule) {
    const value = input.value.trim(), name = input.name.toLowerCase();
    if (!value) return '';
    if (name === 'cpf' && !cpfValid(value)) return 'Confira o CPF: os dígitos verificadores não conferem.';
    if (rule.mask === 'phone' && !/^[1-9]{2}(?:[2-5]\d{7}|9\d{8})$/.test(digits(value))) return 'Informe um telefone válido com DDD e 10 ou 11 dígitos.';
    if (rule.mask === 'cep' && digits(value).length !== 8) return 'O CEP deve ter 8 dígitos.';
    if (rule.mask === 'number' && !/^[1-9]\d{0,5}$/.test(value)) return 'Informe um número de 1 a 999999.';
    if (name === 'nome' && (value.length < 3 || !/\p{L}/u.test(value))) return 'Informe um nome válido com pelo menos 3 caracteres.';
    if (['senha', 'nova_senha'].includes(name) && new TextEncoder().encode(input.value).length > 72) return 'A senha é muito longa. Use uma senha menor.';
    if (name === 'confirmarsenha' || name === 'confirmar_nova_senha') {
      const first = input.form?.elements.namedItem(name === 'confirmarsenha' ? 'senha' : 'nova_senha');
      if (first && input.value !== first.value) return 'As senhas não coincidem.';
    }
    return '';
  }
  document.querySelectorAll('input[name], textarea[name]').forEach(input => {
    const rule = rules[input.name] || rules[input.name.toLowerCase()];
    if (!rule || input.type === 'hidden') return;
    if (rule.type) input.type = rule.type;
    if (rule.max) input.maxLength = Math.min(Number(input.getAttribute('maxlength')) || rule.max, rule.max);
    if (rule.min) input.minLength = rule.min;
    if (rule.autocomplete) input.autocomplete = rule.autocomplete;
    if (rule.minValue) { input.min = rule.minValue; input.max = rule.maxValue; input.step = rule.step; }
    if (rule.mask) input.inputMode = 'numeric';
    if (rule.mask === 'number') input.type = 'text';
    let hint;
    if (rule.hint) {
      hint = document.createElement('small'); hint.className = 'field-hint'; hint.textContent = rule.hint;
      hint.id = 'field-hint-' + (++count);
      input.setAttribute('aria-describedby', ((input.getAttribute('aria-describedby') || '') + ' ' + hint.id).trim());
      (input.closest('.input-group') || input).after(hint);
    }
    const error = document.createElement('small'); error.className = 'field-error'; error.hidden = true;
    error.id = 'field-error-' + (++count); error.setAttribute('aria-live', 'polite');
    input.setAttribute('aria-describedby', ((input.getAttribute('aria-describedby') || '') + ' ' + error.id).trim());
    (hint || input.closest('.input-group') || input).after(error);
    const validate = show => {
      const custom = message(input, rule); input.setCustomValidity(custom);
      const invalid = !input.validity.valid;
      if (show || input.getAttribute('aria-invalid') === 'true') {
        input.setAttribute('aria-invalid', String(invalid));
        error.textContent = invalid ? (custom || input.validationMessage) : ''; error.hidden = !invalid;
      }
    };
    input.addEventListener('input', () => {
      if (rule.mask) {
        const before = input.selectionStart ?? input.value.length;
        const digitCount = digits(input.value.slice(0, before)).length;
        const atEnd = before === input.value.length;
        input.value = format(input.value, rule.mask);
        if (!atEnd) {
          let cursor = 0, passed = 0;
          while (cursor < input.value.length && passed < digitCount) { if (/\d/.test(input.value[cursor])) passed++; cursor++; }
          input.setSelectionRange(cursor, cursor);
        }
      }
      validate(false);
      const confirm = input.form?.elements.namedItem(input.name === 'senha' ? 'confirmarSenha' : 'confirmar_nova_senha');
      if (['senha','nova_senha'].includes(input.name) && confirm && confirm.value) confirm.dispatchEvent(new Event('input'));
    });
    input.addEventListener('blur', () => validate(true));
    input.addEventListener('invalid', () => validate(true));
    if (rule.mask && input.value) input.value = format(input.value,rule.mask);
    input.form?.addEventListener('submit', event => {
      validate(true);
      if (!input.checkValidity()) { event.preventDefault(); input.reportValidity(); }
    });
  });
  document.querySelectorAll('input[type=password]').forEach(input => {
    if (!input.id || input.closest('.input-group')) return;
    const button = document.createElement('button'); button.type='button'; button.className='password-toggle';
    button.textContent='Mostrar senha'; button.setAttribute('aria-controls', input.id); button.setAttribute('aria-pressed','false');
    button.addEventListener('click', () => { const show=input.type==='password';input.type=show?'text':'password';button.textContent=show?'Ocultar senha':'Mostrar senha';button.setAttribute('aria-pressed',String(show)); });
    input.after(button);
  });
  window.togglePassword = id => { const input=document.getElementById(id);if(input) input.type=input.type==='password'?'text':'password'; };
})();
