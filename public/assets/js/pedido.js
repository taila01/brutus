(() => {
  'use strict';
  const endpoint='/brutus/carrinho/itens.php';
  const busy=new Set();
  const announcer=document.createElement('div');
  announcer.className='visually-hidden';announcer.setAttribute('role','status');announcer.setAttribute('aria-live','polite');
  document.body.append(announcer);
  let sequence=0;
  let queue=Promise.resolve();
  function apply(state) {
    document.querySelectorAll('[data-cart-product]').forEach(group => {
      const quantity=Number(state.items?.[group.dataset.cartProduct] || 0);
      const output=group.querySelector('.product-quantity');output.value=String(quantity);output.textContent=String(quantity);output.hidden=quantity===0;
      group.querySelector('.quantity-minus').hidden=quantity===0;
      group.querySelector('.product-added').hidden=quantity===0;
      group.classList.toggle('is-added',quantity>0);
      group.querySelector('[data-cart-action="add"]').disabled=quantity>=99 || busy.has(group.dataset.cartProduct);
    });
    document.querySelectorAll('[data-cart-count]').forEach(counter => {
      counter.textContent=String(state.total_count);counter.hidden=state.total_count===0;
      counter.setAttribute('aria-label',state.total_count+' itens no pedido');
    });
  }
  async function refresh() {
    const request=++sequence;
    try {
      const response=await fetch(endpoint,{credentials:'same-origin',cache:'no-store'});
      const state=await response.json();
      if(response.ok && state.ok && request===sequence)apply(state);
    } catch (_) { /* A contagem inicial permanece disponível. */ }
  }
  document.addEventListener('click',async event => {
    const button=event.target.closest('[data-cart-action]');
    if(!button)return;
    event.preventDefault();
    const group=button.closest('[data-cart-product]'),id=group.dataset.cartProduct;
    if(busy.has(id))return;
    busy.add(id);++sequence;
    const related=Array.from(document.querySelectorAll('[data-cart-product]')).filter(node=>node.dataset.cartProduct===id);
    related.forEach(node=>{node.setAttribute('aria-busy','true');node.querySelector('.cart-item-error').hidden=true;node.querySelectorAll('button').forEach(btn=>btn.disabled=true);});
    try {
      const job=queue.then(async () => {
        const response=await fetch(endpoint,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify({id:Number(id),action:button.dataset.cartAction})});
        const state=await response.json();
        if(!response.ok || !state.ok)throw new Error(state.message || 'Não foi possível atualizar o pedido.');
        return state;
      });
      queue=job.catch(()=>{});
      const state=await job;
      busy.delete(id);apply(state);
      announcer.textContent=group.dataset.productName+': '+state.quantity+' no pedido.';
    } catch(error) {
      related.forEach(node=>{const message=node.querySelector('.cart-item-error');message.textContent=error.message || 'Não foi possível atualizar o pedido. Tente novamente.';message.hidden=false;});
    } finally {
      busy.delete(id);
      related.forEach(node=>{
        node.removeAttribute('aria-busy');
        node.querySelectorAll('button').forEach(btn=>btn.disabled=btn.dataset.cartAction==='add' && Number(node.querySelector('.product-quantity').value)>=99);
      });
    }
  });
  window.addEventListener('pageshow',refresh);
})();
