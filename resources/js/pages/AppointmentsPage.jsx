import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function AppointmentsPage() {
  const { showToast } = useToast();
  const empty = { patient_id: '', doctor_id: '', appointment_date: '', appointment_time: '', status: 'scheduled', is_walk_in: false };
  const [items, setItems] = useState([]);
  const [patients, setPatients] = useState([]);
  const [doctors, setDoctors] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const getErr = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [a, p, d] = await Promise.all([api.get('/appointments'), api.get('/patients'), api.get('/doctors')]);
      setItems(a.data.data || []);
      setPatients(p.data.data || []);
      setDoctors(d.data.data || []);
    } catch (e) {
      showToast(getErr(e), 'error');
    }
  };
  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await api.put(`/appointments/${editingId}`, form);
        showToast('Appointment updated');
      } else {
        await api.post('/appointments', form);
        showToast('Appointment booked');
      }
      setForm(empty);
      setEditingId(null);
      load();
    } catch (e) {
      showToast(getErr(e), 'error');
    }
  };

  const edit = (item) => {
    setEditingId(item.id);
    setForm({
      patient_id: String(item.patient_id || ''),
      doctor_id: String(item.doctor_id || ''),
      appointment_date: item.appointment_date || '',
      appointment_time: (item.appointment_time || '').slice(0, 5),
      status: item.status || 'scheduled',
      is_walk_in: !!item.is_walk_in,
    });
  };

  const remove = async (id) => {
    if (!confirm('Delete this appointment?')) return;
    try {
      await api.delete(`/appointments/${id}`);
      showToast('Appointment deleted');
      load();
    } catch (e) {
      showToast(getErr(e), 'error');
    }
  };

  return <div className="space-y-4">
    <h2 className="text-xl font-bold">Appointments</h2>
    <form onSubmit={submit} className="grid md:grid-cols-3 gap-2 bg-white p-3 rounded shadow">
      <select className="border p-2 rounded" value={form.patient_id} onChange={e=>setForm({...form, patient_id:e.target.value})} required>
        <option value="">Patient</option>
        {patients.map(p=><option key={p.id} value={p.id}>{p.name ? `${p.name} (${p.patient_id})` : p.patient_id}</option>)}
      </select>
      <select className="border p-2 rounded" value={form.doctor_id} onChange={e=>setForm({...form, doctor_id:e.target.value})} required>
        <option value="">Doctor</option>
        {doctors.map(d=><option key={d.id} value={d.id}>{d.name ? `${d.name} (${d.specialization})` : d.specialization}</option>)}
      </select>
      <select className="border p-2 rounded" value={form.status} onChange={e=>setForm({...form, status:e.target.value})}><option>scheduled</option><option>checked_in</option><option>completed</option><option>cancelled</option></select>
      <input type="date" className="border p-2 rounded" value={form.appointment_date} onChange={e=>setForm({...form, appointment_date:e.target.value})} required />
      <input type="time" className="border p-2 rounded" value={form.appointment_time} onChange={e=>setForm({...form, appointment_time:e.target.value})} required />
      <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update' : 'Book'}</button>
      {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
    </form>
    <div className="bg-white rounded shadow overflow-auto">
      <table className="w-full text-sm"><thead><tr className="bg-slate-100"><th className="p-2 text-left">Patient</th><th className="p-2 text-left">Doctor</th><th className="p-2 text-left">Date</th><th className="p-2 text-left">Status</th><th className="p-2 text-left">Actions</th></tr></thead><tbody>{items.map(i=><tr key={i.id} className="border-t"><td className="p-2">{(patients.find(p => p.id === i.patient_id)?.name) || `Patient #${i.patient_id}`}</td><td className="p-2">{(doctors.find(d => d.id === i.doctor_id)?.name) || `Doctor #${i.doctor_id}`}</td><td className="p-2">{i.appointment_date} {String(i.appointment_time || '').slice(0,5)}</td><td className="p-2">{i.status}</td><td className="p-2 space-x-2"><button className="text-blue-700" onClick={()=>edit(i)}>Edit</button><button className="text-red-700" onClick={()=>remove(i.id)}>Delete</button></td></tr>)}</tbody></table>
    </div>
  </div>;
}
