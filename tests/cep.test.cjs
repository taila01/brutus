const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync(__dirname + '/../cadastro/cep.js', 'utf8');
class Field extends EventTarget {
  constructor() { super(); this.value = ''; this.dataset = {}; this.attributes = {}; this.children = []; }
  after(node) { this.children.push(node); }
  setAttribute(name, value) { this.attributes[name] = value; }
  getAttribute(name) { return this.attributes[name] || null; }
  removeAttribute(name) { delete this.attributes[name]; }
}
function setup(fetch, uppercase = false) {
  const cep = new Field(), fields = { rua: new Field(), bairro: new Field(), cidade: new Field(), estado: new Field() };
  const document = {
    getElementById(id) { return id === (uppercase ? 'CEP' : 'cep') ? cep : fields[id] || null; },
    createElement() { return new Field(); }
  };
  vm.runInNewContext(source, { document, fetch, Event, AbortController, DOMException, setTimeout, clearTimeout, Map, Set });
  return { cep, fields, button: cep.children[0], status: cep.children[0].children[0] };
}
const wait = ms => new Promise(resolve => setTimeout(resolve, ms));
const success = address => ({ ok: true, json: async () => address });
const address = { logradouro: 'Avenida Paulista', bairro: 'Bela Vista', localidade: 'São Paulo', uf: 'SP' };
async function lookup(ui, cep = '01310-000') {
  ui.cep.value = cep; ui.cep.dispatchEvent(new Event('blur')); await wait(10);
}
test('BrasilCEP preenche endereço e usa cache', async () => {
  let calls = 0;
  const ui = setup(async url => { calls++; assert.match(url, /api\.brasilcep\.dev/); return success(address); });
  await lookup(ui);
  assert.equal(ui.fields.rua.value, 'Avenida Paulista'); assert.equal(ui.fields.cidade.value, 'São Paulo');
  assert.equal(ui.fields.estado.value, 'SP'); assert.equal(ui.status.dataset.state, 'success');
  await lookup(ui); assert.equal(calls, 1);
});
test('BrasilAPI assume consulta quando BrasilCEP falha', async () => {
  const urls = [];
  const ui = setup(async url => { urls.push(url); if (url.includes('brasilcep')) throw new TypeError('Network error'); return success({ street: 'Avenida Paulista', neighborhood: 'Bela Vista', city: 'São Paulo', state: 'SP' }); }, true);
  await lookup(ui); assert.equal(urls.length, 2); assert.match(urls[1], /brasilapi/); assert.equal(ui.fields.cidade.value, 'São Paulo');
});
test('consulta automática só ocorre ao completar oito dígitos', async () => {
  let calls = 0; const ui = setup(async () => { calls++; return success(address); });
  ui.cep.value='01310';ui.cep.dispatchEvent(new Event('input'));await wait(380);assert.equal(calls,0);
  ui.cep.value='01310-000';ui.cep.dispatchEvent(new Event('input'));await wait(380);assert.equal(calls,1);
});
test('não apaga endereço manual ou número e complemento', async () => {
  const ui = setup(async () => success(address));
  ui.fields.rua.value='Rua informada pelo cliente';ui.fields.rua.dispatchEvent(new Event('input'));
  await lookup(ui);assert.equal(ui.fields.rua.value,'Rua informada pelo cliente');assert.equal(ui.fields.bairro.value,'Bela Vista');
});
test('CEP inexistente mostra aviso e permite preenchimento manual', async () => {
  const ui=setup(async () => ({ok:false,status:404}));await lookup(ui,'00000-000');
  assert.equal(ui.status.dataset.state,'error');assert.match(ui.status.textContent,/não encontrado/);assert.equal(ui.button.disabled,false);
});
test('falha de rede mostra erro e libera uma nova consulta',async () => {
  const ui=setup(async () => {throw new TypeError('offline');});await lookup(ui);
  assert.match(ui.status.textContent,/manualmente/);assert.equal(ui.button.disabled,false);assert.equal(ui.cep.getAttribute('aria-busy'),null);
});
test('resposta de CEP anterior não sobrescreve o novo endereço',async () => {
  let resolveOld;
  const ui=setup(url => url.endsWith('01310000') ? new Promise(resolve => {resolveOld=resolve;}) : Promise.resolve(success({...address,logradouro:'Rua nova'})));
  ui.cep.value='01310000';ui.cep.dispatchEvent(new Event('blur'));await wait(5);
  ui.cep.value='12200000';ui.cep.dispatchEvent(new Event('input'));ui.cep.dispatchEvent(new Event('blur'));await wait(10);
  resolveOld(success(address));await wait(10);assert.equal(ui.fields.rua.value,'Rua nova');
});
