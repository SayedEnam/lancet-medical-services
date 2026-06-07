import React, { useEffect, useMemo, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function InventoryPage() {
  const { showToast } = useToast();

  const emptySupplier = { name: '', phone: '', email: '', address: '' };
  const emptyItem = { name: '', sku: '', category: '', stock: 0, low_stock_threshold: 10, unit_price: 0, expiry_date: '' };
  const emptyPurchase = { supplier_id: '', inventory_item_id: '', quantity: '', unit_cost: '', purchase_date: '', invoice_ref: '' };

  const [suppliers, setSuppliers] = useState([]);
  const [items, setItems] = useState([]);
  const [purchases, setPurchases] = useState([]);

  const [supplierForm, setSupplierForm] = useState(emptySupplier);
  const [itemForm, setItemForm] = useState(emptyItem);
  const [purchaseForm, setPurchaseForm] = useState(emptyPurchase);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [sRes, iRes, pRes] = await Promise.all([
        api.get('/suppliers'),
        api.get('/inventory-items'),
        api.get('/purchases'),
      ]);
      setSuppliers(sRes.data.data || []);
      setItems(iRes.data.data || []);
      setPurchases(pRes.data.data || []);
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  useEffect(() => { load(); }, []);

  const addSupplier = async (e) => {
    e.preventDefault();
    try { await api.post('/suppliers', supplierForm); showToast('Supplier added'); setSupplierForm(emptySupplier); load(); } catch (e) { showToast(err(e), 'error'); }
  };

  const addItem = async (e) => {
    e.preventDefault();
    try { await api.post('/inventory-items', itemForm); showToast('Item added'); setItemForm(emptyItem); load(); } catch (e) { showToast(err(e), 'error'); }
  };

  const addPurchase = async (e) => {
    e.preventDefault();
    try { await api.post('/purchases', purchaseForm); showToast('Purchase recorded & stock updated'); setPurchaseForm(emptyPurchase); load(); } catch (e) { showToast(err(e), 'error'); }
  };

  const lowStock = useMemo(() => items.filter(i => Number(i.stock) <= Number(i.low_stock_threshold)), [items]);

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Inventory</h1>

    <div className="grid md:grid-cols-3 gap-3">
      <div className="bg-white rounded-xl shadow p-4"><p className="text-sm text-slate-500">Total Items</p><p className="text-2xl font-semibold">{items.length}</p></div>
      <div className="bg-white rounded-xl shadow p-4"><p className="text-sm text-slate-500">Suppliers</p><p className="text-2xl font-semibold">{suppliers.length}</p></div>
      <div className="bg-white rounded-xl shadow p-4"><p className="text-sm text-slate-500">Low Stock Alerts</p><p className="text-2xl font-semibold text-red-600">{lowStock.length}</p></div>
    </div>

    <section className="bg-white rounded-xl shadow p-4 space-y-2">
      <h2 className="text-lg font-semibold">Add Supplier</h2>
      <form onSubmit={addSupplier} className="grid md:grid-cols-4 gap-2">
        <input className="border p-2 rounded" placeholder="Name" value={supplierForm.name} onChange={e=>setSupplierForm({...supplierForm, name:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Phone" value={supplierForm.phone} onChange={e=>setSupplierForm({...supplierForm, phone:e.target.value})} />
        <input className="border p-2 rounded" placeholder="Email" value={supplierForm.email} onChange={e=>setSupplierForm({...supplierForm, email:e.target.value})} />
        <input className="border p-2 rounded" placeholder="Address" value={supplierForm.address} onChange={e=>setSupplierForm({...supplierForm, address:e.target.value})} />
        <button className="bg-slate-900 text-white p-2 rounded">Save Supplier</button>
      </form>
    </section>

    <section className="bg-white rounded-xl shadow p-4 space-y-2">
      <h2 className="text-lg font-semibold">Add Inventory Item</h2>
      <form onSubmit={addItem} className="grid md:grid-cols-4 gap-2">
        <input className="border p-2 rounded" placeholder="Item Name" value={itemForm.name} onChange={e=>setItemForm({...itemForm, name:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="SKU" value={itemForm.sku} onChange={e=>setItemForm({...itemForm, sku:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Category" value={itemForm.category} onChange={e=>setItemForm({...itemForm, category:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="Opening Stock" value={itemForm.stock} onChange={e=>setItemForm({...itemForm, stock:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="Low Stock Threshold" value={itemForm.low_stock_threshold} onChange={e=>setItemForm({...itemForm, low_stock_threshold:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="Unit Price" value={itemForm.unit_price} onChange={e=>setItemForm({...itemForm, unit_price:e.target.value})} />
        <input type="date" className="border p-2 rounded" value={itemForm.expiry_date} onChange={e=>setItemForm({...itemForm, expiry_date:e.target.value})} />
        <button className="bg-slate-900 text-white p-2 rounded">Save Item</button>
      </form>
    </section>

    <section className="bg-white rounded-xl shadow p-4 space-y-2">
      <h2 className="text-lg font-semibold">Record Purchase</h2>
      <form onSubmit={addPurchase} className="grid md:grid-cols-4 gap-2">
        <select className="border p-2 rounded" value={purchaseForm.supplier_id} onChange={e=>setPurchaseForm({...purchaseForm, supplier_id:e.target.value})}><option value="">Supplier (optional)</option>{suppliers.map(s=><option key={s.id} value={s.id}>{s.name}</option>)}</select>
        <select className="border p-2 rounded" value={purchaseForm.inventory_item_id} onChange={e=>setPurchaseForm({...purchaseForm, inventory_item_id:e.target.value})} required><option value="">Inventory Item</option>{items.map(i=><option key={i.id} value={i.id}>{i.name} ({i.sku})</option>)}</select>
        <input type="number" className="border p-2 rounded" placeholder="Quantity" value={purchaseForm.quantity} onChange={e=>setPurchaseForm({...purchaseForm, quantity:e.target.value})} required />
        <input type="number" className="border p-2 rounded" placeholder="Unit Cost" value={purchaseForm.unit_cost} onChange={e=>setPurchaseForm({...purchaseForm, unit_cost:e.target.value})} required />
        <input type="date" className="border p-2 rounded" value={purchaseForm.purchase_date} onChange={e=>setPurchaseForm({...purchaseForm, purchase_date:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Invoice Ref" value={purchaseForm.invoice_ref} onChange={e=>setPurchaseForm({...purchaseForm, invoice_ref:e.target.value})} />
        <button className="bg-slate-900 text-white p-2 rounded">Save Purchase</button>
      </form>
    </section>

    <section className="bg-white rounded-xl shadow overflow-auto">
      <h3 className="font-semibold p-4">Inventory Stock</h3>
      <table className="w-full text-sm">
        <thead><tr className="bg-slate-100"><th className="p-2 text-left">Item</th><th className="p-2 text-left">SKU</th><th className="p-2 text-left">Category</th><th className="p-2 text-left">Stock</th><th className="p-2 text-left">Threshold</th></tr></thead>
        <tbody>{items.map(i => <tr key={i.id} className="border-t"><td className="p-2">{i.name}</td><td className="p-2">{i.sku}</td><td className="p-2">{i.category}</td><td className={`p-2 ${Number(i.stock) <= Number(i.low_stock_threshold) ? 'text-red-600 font-semibold' : ''}`}>{i.stock}</td><td className="p-2">{i.low_stock_threshold}</td></tr>)}</tbody>
      </table>
    </section>
  </div>;
}
