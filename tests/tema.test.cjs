const { test } = require('node:test');
const assert = require('node:assert/strict');
const vm = require('node:vm');
const fs = require('node:fs');
const source = fs.readFileSync(__dirname + '/../assets/tema.js','utf8');
function setup(saved, blocked=false) {
 const docListeners={}, winListeners={}, attributes={}, store={ 'brutus-theme':saved };
 const button={setAttribute(name,value){attributes[name]=value;}};
 const document={documentElement:{dataset:{}},querySelectorAll(){return [button];},addEventListener(name,fn){docListeners[name]=fn;}};
 const window={addEventListener(name,fn){winListeners[name]=fn;}};
 const localStorage={getItem(key){if(blocked)throw Error('blocked');return store[key];},setItem(key,value){if(blocked)throw Error('blocked');store[key]=value;}};
 vm.runInNewContext(source,{document,window,localStorage});
 return {document,attributes,store,click(){docListeners.click({target:{closest(){return button;}}});},storage(event){winListeners.storage(event);}};
}
test('modo claro inicial e alternância completa',()=>{
 const ui=setup(null);assert.equal(ui.document.documentElement.dataset.theme,'light');
 assert.equal(ui.attributes['aria-label'],'Ativar modo noturno');
 ui.click();assert.equal(ui.document.documentElement.dataset.theme,'dark');assert.equal(ui.store['brutus-theme'],'dark');assert.equal(ui.attributes['aria-label'],'Ativar modo claro');assert.equal(ui.attributes['aria-pressed'],'true');
 ui.click();assert.equal(ui.document.documentElement.dataset.theme,'light');assert.equal(ui.store['brutus-theme'],'light');
});
test('recupera modo escuro salvo antes da renderização',()=>{
 const ui=setup('dark');assert.equal(ui.document.documentElement.dataset.theme,'dark');
});
test('valor salvo inválido mantém padrão claro',()=>{assert.equal(setup('invalido').document.documentElement.dataset.theme,'light');});
test('alternância funciona com armazenamento bloqueado',()=>{const ui=setup(null,true);ui.click();assert.equal(ui.document.documentElement.dataset.theme,'dark');});
test('sincroniza outras abas e limpeza da preferência',()=>{
 const ui=setup(null);ui.storage({key:'brutus-theme',newValue:'dark'});assert.equal(ui.document.documentElement.dataset.theme,'dark');
 ui.storage({key:'outra-chave',newValue:'light'});assert.equal(ui.document.documentElement.dataset.theme,'dark');
 ui.storage({key:null,newValue:null});assert.equal(ui.document.documentElement.dataset.theme,'light');
});
