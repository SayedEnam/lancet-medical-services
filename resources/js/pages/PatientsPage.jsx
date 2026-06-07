import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function PatientsPage() {
  const { showToast } = useToast();
  const empty = { name: '', email: '', phone: '', patient_id: '', gender: 'male', blood_group: '', emergency_contact: '' };
  const [items, setItems] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const getErr = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const r = await api.get('/patients');
      setItems(r.data.data || []);
    } catch (e) { showToast(getErr(e), 'error'); }
  };

  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) { await api.put(`/patients/${editingId}`, form); showToast('Patient updated'); }
      else { await api.post('/patients', form); showToast('Patient added'); }
      setForm(empty); setEditingId(null); load();
    } catch (e) { showToast(getErr(e), 'error'); }
  };

  const edit = (item) => {
    setEditingId(item.id);
    setForm({ name: item.name || '', email: item.email || '', phone: item.phone || '', patient_id: item.patient_id || '', gender: item.gender || 'male', blood_group: item.blood_group || '', emergency_contact: item.emergency_contact || '' });
  };

  const remove = async (id) => {
    if (!confirm('Delete this patient?')) return;
    try { await api.delete(`/patients/${id}`); showToast('Patient deleted'); load(); } catch (e) { showToast(getErr(e), 'error'); }
  };

  return <div className="space-y-4">
    <h2 className="text-xl font-bold">Patients</h2>
    <form onSubmit={submit} className="grid md:grid-cols-4 gap-2 bg-white p-3 rounded shadow">
      <input className="border p-2 rounded" placeholder="Patient Name" value={form.name} onChange={e=>setForm({...form, name:e.target.value})} required />
      <input className="border p-2 rounded" placeholder="Email" value={form.email} onChange={e=>setForm({...form, email:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Phone" value={form.phone} onChange={e=>setForm({...form, phone:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Patient ID" value={form.patient_id} onChange={e=>setForm({...form, patient_id:e.target.value})} required />
      <select className="border p-2 rounded" value={form.gender} onChange={e=>setForm({...form, gender:e.target.value})}><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
      <input className="border p-2 rounded" placeholder="Blood Group" value={form.blood_group} onChange={e=>setForm({...form, blood_group:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Emergency Contact" value={form.emergency_contact} onChange={e=>setForm({...form, emergency_contact:e.target.value})} />
      <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update Patient' : 'Add Patient'}</button>
      {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
    </form>
    <div className="bg-white rounded shadow overflow-auto">
      <table className="w-full text-sm"><thead><tr className="bg-slate-100"><th className="p-2 text-left">Patient Name</th><th className="p-2 text-left">Patient ID</th><th className="p-2 text-left">Phone</th><th className="p-2 text-left">Gender</th><th className="p-2 text-left">Actions</th></tr></thead><tbody>{items.map(i=><tr key={i.id} className="border-t"><td className="p-2">{i.name}</td><td className="p-2">{i.patient_id}</td><td className="p-2">{i.phone}</td><td className="p-2">{i.gender}</td><td className="p-2 space-x-2"><button className="text-blue-700" onClick={()=>edit(i)}>Edit</button><button className="text-red-700" onClick={()=>remove(i.id)}>Delete</button></td></tr>)}</tbody></table>
    </div>
  </div>;
}
