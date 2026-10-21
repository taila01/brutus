(() => {
  'use strict';
  const input = document.getElementById('cep') || document.getElementById('CEP');
  if (!input) return;
  const fields = Object.fromEntries(['rua', 'bairro', 'cidade', 'estado'].map(name => [name, document.getElementById(name)]));
  const edited = new Set();
  const cache = new Map();
  let sequence = 0, controller, debounce, filling = false, pendingCep = '';
  const button = document.createElement('button');
  button.type = 'button'; button.className = 'cep-search-button'; button.textContent = 'Buscar CEP';
  const status = document.createElement('small');
  status.id = 'cep-search-status'; status.className = 'cep-search-status';
  status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
  input.after(button); button.after(status);
  input.setAttribute('aria-describedby', ((input.getAttribute('aria-describedby') || '') + ' ' + status.id).trim());
  Object.entries(fields).forEach(([name, field]) => {
    field?.addEventListener('input', () => { if (!filling) edited.add(name); });
  });
  const digits = () => input.value.replace(/\D/g, '');
  function show(message, state = '') {
    status.textContent = message; status.dataset.state = state;
  }
  function normalize(data) {
    if (!data || typeof data !== 'object' || data.erro || data.errors) throw new Error('invalid-response');
    const read = (...keys) => {
      for (const key of keys) if (typeof data[key] === 'string') return data[key].trim();
      return '';
    };
    const address = { rua: read('logradouro', 'street'), bairro: read('bairro', 'neighborhood'), cidade: read('localidade', 'city'), estado: read('uf', 'state') };
    if (!address.cidade) throw new Error('invalid-response');
    return address;
  }
  async function fetchAddress(cep, parentSignal) {
    const urls = [
      'https://api.brasilcep.dev/v1/' + cep,
      'https://brasilapi.com.br/api/cep/v1/' + cep
    ];
    let lastError;
    for (const url of urls) {
      if (parentSignal.aborted) throw new DOMException('Aborted', 'AbortError');
      const attempt = new AbortController();
      const cancel = () => attempt.abort();
      parentSignal.addEventListener('abort', cancel, { once: true });
      const timeout = setTimeout(cancel, 4000);
      try {
        const response = await fetch(url, { signal: attempt.signal, headers: { Accept: 'application/json' } });
        if (!response.ok) {
          const error = new Error('http-error'); error.notFound = response.status === 404; throw error;
        }
        return normalize(await response.json());
      } catch (error) {
        if (parentSignal.aborted) throw new DOMException('Aborted', 'AbortError');
        lastError = error;
      } finally {
        clearTimeout(timeout); parentSignal.removeEventListener('abort', cancel);
      }
    }
    throw lastError || new Error('unavailable');
  }
  function fill(address) {
    filling = true;
    try {
      Object.entries(address).forEach(([name, value]) => {
        const field = fields[name];
        if (!field || !value || (edited.has(name) && field.value.trim())) return;
        field.value = value;
        field.dispatchEvent(new Event('input', { bubbles: true }));
      });
    } finally { filling = false; }
  }
  async function search() {
    clearTimeout(debounce);
    const cep = digits();
    if (cep.length !== 8) { show('Digite os 8 dígitos do CEP.', 'error'); return; }
    if (pendingCep === cep) return;
    const request = ++sequence;
    controller?.abort(); controller = new AbortController(); pendingCep = cep;
    input.setAttribute('aria-busy', 'true'); button.disabled = true;
    show('Buscando endereço…', 'loading');
    try {
      const address = cache.get(cep) || await fetchAddress(cep, controller.signal);
      if (request !== sequence || digits() !== cep) return;
      cache.set(cep, address); fill(address);
      show(address.rua && address.bairro ? 'Endereço encontrado. Confira os dados e informe o número.' : 'Cidade encontrada. Complete a rua, o bairro e o número.', 'success');
    } catch (error) {
      if (request !== sequence || error.name === 'AbortError') return;
      show(error.notFound ? 'CEP não encontrado. Confira o número ou preencha o endereço manualmente.' : 'Não foi possível consultar o CEP agora. Tente novamente ou preencha o endereço manualmente.', 'error');
    } finally {
      if (request === sequence) { pendingCep = ''; button.disabled = false; input.removeAttribute('aria-busy'); }
    }
  }
  input.addEventListener('input', () => {
    clearTimeout(debounce);
    const cep = digits();
    if (cep !== pendingCep) {
      sequence++; controller?.abort(); pendingCep = ''; button.disabled = false; input.removeAttribute('aria-busy');
    }
    show('');
    if (cep.length === 8) debounce = setTimeout(search, 350);
  });
  input.addEventListener('blur', () => { if (digits().length === 8) search(); });
  button.addEventListener('click', search);
})();
