import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function TestsPage() {
  const { showToast } = useToast();
  const emptyCategory = { name: '', description: '' };
  const emptyTest = { test_category_id: '', name: '', price: 0, sample_type: '', report_delivery_time: '', instructions: '', is_popular: false, home_collection_available: false };

  const [categories, setCategories] = useState([]);
  const [tests, setTests] = useState([]);
  const [categoryForm, setCategoryForm] = useState(emptyCategory);
  const [testForm, setTestForm] = useState(emptyTest);
  const [editingCategoryId, setEditingCategoryId] = useState(null);
  const [editingTestId, setEditingTestId] = useState(null);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [catRes, testRes] = await Promise.all([api.get('/test-categories'), api.get('/medical-tests')]);
      setCategories(catRes.data.data || []);
      setTests(testRes.data.data || []);
    } catch (e) { showToast(err(e), 'error'); }
  };

  useEffect(() => { load(); }, []);

  const submitCategory = async (e) => {
    e.preventDefault();
    try {
      if (editingCategoryId) { await api.put(`/test-categories/${editingCategoryId}`, categoryForm); showToast('Category updated'); }
      else { await api.post('/test-categories', categoryForm); showToast('Category added'); }
      setCategoryForm(emptyCategory); setEditingCategoryId(null); load();
    } catch (e) { showToast(err(e), 'error'); }
  };

  const submitTest = async (e) => {
    e.preventDefault();
    try {
      if (editingTestId) { await api.put(`/medical-tests/${editingTestId}`, testForm); showToast('Test updated'); }
      else { await api.post('/medical-tests', testForm); showToast('Test created'); }
      setTestForm(emptyTest); setEditingTestId(null); load();
    } catch (e) { showToast(err(e), 'error'); }
  };

  const editCategory = (c) => { setEditingCategoryId(c.id); setCategoryForm({ name: c.name || '', description: c.description || '' }); };
  const editTest = (t) => { setEditingTestId(t.id); setTestForm({ test_category_id: String(t.test_category_id || ''), name: t.name || '', price: t.price || 0, sample_type: t.sample_type || '', report_delivery_time: t.report_delivery_time || '', instructions: t.instructions || '', is_popular: !!t.is_popular, home_collection_available: !!t.home_collection_available }); };

  const delCategory = async (id) => { if (!confirm('Delete this category?')) return; try { await api.delete(`/test-categories/${id}`); showToast('Category deleted'); load(); } catch (e) { showToast(err(e), 'error'); } };
  const delTest = async (id) => { if (!confirm('Delete this test?')) return; try { await api.delete(`/medical-tests/${id}`); showToast('Test deleted'); load(); } catch (e) { showToast(err(e), 'error'); } };

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Tests Module</h1>
    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Test Categories</h2>
      <form onSubmit={submitCategory} className="grid md:grid-cols-3 gap-2">
        <input className="border p-2 rounded" placeholder="Category Name" value={categoryForm.name} onChange={e=>setCategoryForm({...categoryForm,name:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Description" value={categoryForm.description} onChange={e=>setCategoryForm({...categoryForm,description:e.target.value})} />
        <div className="flex gap-2"><button className="bg-slate-900 text-white px-3 rounded">{editingCategoryId ? 'Update' : 'Add'}</button>{editingCategoryId && <button type="button" className="bg-slate-200 px-3 rounded" onClick={() => { setEditingCategoryId(null); setCategoryForm(emptyCategory); }}>Cancel</button>}</div>
      </form>
      <table className="w-full text-sm"><thead><tr className="bg-slate-100"><th className="p-2 text-left">Name</th><th className="p-2 text-left">Description</th><th className="p-2 text-left">Action</th></tr></thead><tbody>{categories.map(c=><tr key={c.id} className="border-t"><td className="p-2">{c.name}</td><td className="p-2">{c.description}</td><td className="p-2 space-x-2"><button className="text-blue-700" onClick={()=>editCategory(c)}>Edit</button><button className="text-red-700" onClick={()=>delCategory(c.id)}>Delete</button></td></tr>)}</tbody></table>
    </section>

    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Medical Tests</h2>
      <form onSubmit={submitTest} className="grid md:grid-cols-4 gap-2">
        <select className="border p-2 rounded" value={testForm.test_category_id} onChange={e=>setTestForm({...testForm,test_category_id:e.target.value})} required><option value="">Category</option>{categories.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}</select>
        <input className="border p-2 rounded" placeholder="Test Name" value={testForm.name} onChange={e=>setTestForm({...testForm,name:e.target.value})} required />
        <input type="number" className="border p-2 rounded" placeholder="Price" value={testForm.price} onChange={e=>setTestForm({...testForm,price:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Sample Type" value={testForm.sample_type} onChange={e=>setTestForm({...testForm,sample_type:e.target.value})} />
        <input className="border p-2 rounded" placeholder="Report Delivery Time" value={testForm.report_delivery_time} onChange={e=>setTestForm({...testForm,report_delivery_time:e.target.value})} />
        <input className="border p-2 rounded md:col-span-2" placeholder="Instructions" value={testForm.instructions} onChange={e=>setTestForm({...testForm,instructions:e.target.value})} />
        <label className="flex items-center gap-2"><input type="checkbox" checked={testForm.is_popular} onChange={e=>setTestForm({...testForm,is_popular:e.target.checked})} /> Popular</label>
        <label className="flex items-center gap-2"><input type="checkbox" checked={testForm.home_collection_available} onChange={e=>setTestForm({...testForm,home_collection_available:e.target.checked})} /> Home Collection</label>
        <div className="flex gap-2 md:col-span-4"><button className="bg-slate-900 text-white px-3 py-2 rounded">{editingTestId ? 'Update Test' : 'Add Test'}</button>{editingTestId && <button type="button" className="bg-slate-200 px-3 py-2 rounded" onClick={() => { setEditingTestId(null); setTestForm(emptyTest); }}>Cancel</button>}</div>
      </form>
      <table className="w-full text-sm"><thead><tr className="bg-slate-100"><th className="p-2 text-left">Name</th><th className="p-2 text-left">Category</th><th className="p-2 text-left">Price</th><th className="p-2 text-left">Sample</th><th className="p-2 text-left">Actions</th></tr></thead><tbody>{tests.map(t=><tr key={t.id} className="border-t"><td className="p-2">{t.name}</td><td className="p-2">{t.category?.name || '-'}</td><td className="p-2">{t.price}</td><td className="p-2">{t.sample_type}</td><td className="p-2 space-x-2"><button className="text-blue-700" onClick={()=>editTest(t)}>Edit</button><button className="text-red-700" onClick={()=>delTest(t.id)}>Delete</button></td></tr>)}</tbody></table>
    </section>
  </div>;
}
