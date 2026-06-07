import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function DoctorsPage() {
  const { showToast } = useToast();
  const empty = { name: '', email: '', phone: '', specialization: '', consultation_fee: 0, commission_percent: 0, is_available: true };
  const [items, setItems] = useState([]);
  const [search, setSearch] = useState('');
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const getErr = (e) => e?.response?.data?.message || 'Operation failed';
  const load = async () => { try { const r = await api.get('/doctors', { params: search ? { search } : {} }); setItems(r.data.data || []);} catch (e) { showToast(getErr(e), 'error'); } };
  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try { if (editingId) { await api.put(`/doctors/${editingId}`, form); showToast('Doctor updated'); } else { await api.post('/doctors', form); showToast('Doctor added'); }
      setForm(empty); setEditingId(null); load(); } catch (e) { showToast(getErr(e), 'error'); }
  };

  const edit = (item) => { setEditingId(item.id); setForm({ name: item.name || '', email: item.email || '', phone: item.phone || '', specialization: item.specialization || '', consultation_fee: item.consultation_fee || 0, commission_percent: item.commission_percent || 0, is_available: !!item.is_available }); };
  const remove = async (id) => { if (!confirm('Delete this doctor?')) return; try { await api.delete(`/doctors/${id}`); showToast('Doctor deleted'); load(); } catch (e) { showToast(getErr(e), 'error'); } };

  return <div className="space-y-4">
    <h2 className="text-xl font-bold">Doctors</h2>
    <div className="bg-white p-3 rounded shadow"><input className="border p-2 rounded w-full md:w-96" placeholder="Search by doctor name or specialization" value={search} onChange={e=>setSearch(e.target.value)} onKeyDown={e=>e.key==='Enter'&&load()} /><button className="ml-2 bg-slate-700 text-white p-2 rounded" onClick={load}>Search</button></div>
    <form onSubmit={submit} className="grid md:grid-cols-4 gap-2 bg-white p-3 rounded shadow">
      <input className="border p-2 rounded" placeholder="Doctor Name" value={form.name} onChange={e=>setForm({...form, name:e.target.value})} required />
      <input className="border p-2 rounded" placeholder="Email" value={form.email} onChange={e=>setForm({...form, email:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Phone" value={form.phone} onChange={e=>setForm({...form, phone:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Specialization" value={form.specialization} onChange={e=>setForm({...form, specialization:e.target.value})} required />
      <input type="number" className="border p-2 rounded" placeholder="Consultation Fee" value={form.consultation_fee} onChange={e=>setForm({...form, consultation_fee:e.target.value})} />
      <input type="number" className="border p-2 rounded" placeholder="Commission %" value={form.commission_percent} onChange={e=>setForm({...form, commission_percent:e.target.value})} />
      <select className="border p-2 rounded" value={form.is_available ? '1' : '0'} onChange={e=>setForm({...form, is_available:e.target.value==='1'})}><option value="1">Available</option><option value="0">Unavailable</option></select>
      <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update Doctor' : 'Add Doctor'}</button>
      {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
    </form>
    <div className="bg-white rounded shadow overflow-auto">
      <table className="w-full text-sm"><thead><tr className="bg-slate-100"><th className="p-2 text-left">Name</th><th className="p-2 text-left">Phone</th><th className="p-2 text-left">Specialization</th><th className="p-2 text-left">Actions</th></tr></thead><tbody>{items.map(i=><tr key={i.id} className="border-t"><td className="p-2">{i.name}</td><td className="p-2">{i.phone}</td><td className="p-2">{i.specialization}</td><td className="p-2 space-x-2"><button className="text-blue-700" onClick={()=>edit(i)}>Edit</button><button className="text-red-700" onClick={()=>remove(i.id)}>Delete</button></td></tr>)}</tbody></table>
    </div>
  </div>;
}
