import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function StaffPage() {
  const { showToast } = useToast();
  const empty = { user_id: '', designation: '', salary: '', joining_date: '', employment_status: 'active' };

  const [users, setUsers] = useState([]);
  const [employees, setEmployees] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [uRes, eRes] = await Promise.all([api.get('/auth/me').then(() => api.get('/employees')), api.get('/employees')]);
      setUsers(uRes.data?.data ? [] : []);
      setEmployees(eRes.data.data || []);
    } catch (e) {
      try {
        const eRes = await api.get('/employees');
        setEmployees(eRes.data.data || []);
      } catch (inner) {
        showToast(err(inner), 'error');
      }
    }
  };

  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try {
      const payload = { ...form, user_id: form.user_id || null };
      if (editingId) {
        await api.put(`/employees/${editingId}`, payload);
        showToast('Staff updated');
      } else {
        await api.post('/employees', payload);
        showToast('Staff added');
      }
      setEditingId(null);
      setForm(empty);
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const edit = (item) => {
    setEditingId(item.id);
    setForm({
      user_id: item.user_id ? String(item.user_id) : '',
      designation: item.designation || '',
      salary: item.salary || '',
      joining_date: item.joining_date || '',
      employment_status: item.employment_status || 'active',
    });
  };

  const remove = async (id) => {
    if (!confirm('Delete this staff record?')) return;
    try {
      await api.delete(`/employees/${id}`);
      showToast('Staff deleted');
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Staff</h1>

    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Employee Entry</h2>
      <form onSubmit={submit} className="grid md:grid-cols-3 gap-2">
        <select className="border p-2 rounded" value={form.user_id} onChange={e=>setForm({...form, user_id:e.target.value})}>
          <option value="">Linked User (optional)</option>
          {users.map(u => <option key={u.id} value={u.id}>{u.name} ({u.email})</option>)}
        </select>
        <input className="border p-2 rounded" placeholder="Designation" value={form.designation} onChange={e=>setForm({...form, designation:e.target.value})} required />
        <input type="number" className="border p-2 rounded" placeholder="Salary" value={form.salary} onChange={e=>setForm({...form, salary:e.target.value})} required />
        <input type="date" className="border p-2 rounded" value={form.joining_date} onChange={e=>setForm({...form, joining_date:e.target.value})} />
        <select className="border p-2 rounded" value={form.employment_status} onChange={e=>setForm({...form, employment_status:e.target.value})}>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
          <option value="on_leave">On Leave</option>
        </select>
        <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update Staff' : 'Add Staff'}</button>
        {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
      </form>
    </section>

    <section className="bg-white rounded-xl shadow overflow-auto">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-slate-100">
            <th className="p-2 text-left">User</th>
            <th className="p-2 text-left">Designation</th>
            <th className="p-2 text-left">Salary</th>
            <th className="p-2 text-left">Joining Date</th>
            <th className="p-2 text-left">Status</th>
            <th className="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          {employees.map(emp => (
            <tr key={emp.id} className="border-t">
              <td className="p-2">{emp.user?.name || '-'}</td>
              <td className="p-2">{emp.designation}</td>
              <td className="p-2">{Number(emp.salary).toFixed(2)}</td>
              <td className="p-2">{emp.joining_date || '-'}</td>
              <td className="p-2">{emp.employment_status}</td>
              <td className="p-2 space-x-2">
                <button className="text-blue-700" onClick={() => edit(emp)}>Edit</button>
                <button className="text-red-700" onClick={() => remove(emp.id)}>Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </section>
  </div>;
}

