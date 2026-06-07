import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function ReportsPage() {
  const { showToast } = useToast();
  const empty = { test_order_id: '', type: 'hematology', status: 'processing', digital_signature: '' };
  const [items, setItems] = useState([]);
  const [testOrders, setTestOrders] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [rRes, tRes] = await Promise.all([api.get('/reports'), api.get('/test-orders')]);
      setItems(rRes.data.data || []);
      setTestOrders(tRes.data.data || []);
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await api.put(`/reports/${editingId}`, form);
        showToast('Report updated');
      } else {
        await api.post('/reports', form);
        showToast('Report created');
      }
      setForm(empty);
      setEditingId(null);
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const edit = (r) => {
    setEditingId(r.id);
    setForm({
      test_order_id: String(r.test_order_id || ''),
      type: r.type || 'hematology',
      status: r.status || 'processing',
      digital_signature: r.digital_signature || '',
    });
  };

  const remove = async (id) => {
    if (!confirm('Delete this report?')) return;
    try {
      await api.delete(`/reports/${id}`);
      showToast('Report deleted');
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const setStatus = async (id, status) => {
    try {
      await api.post(`/reports/${id}/status`, { status });
      showToast('Status updated');
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const generatePdf = async (id) => {
    try {
      const { data } = await api.post(`/reports/${id}/generate-pdf`);
      showToast('PDF generated');
      if (data.download_url) {
        window.open(data.download_url, '_blank');
      }
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  return <div className="space-y-4">
    <h1 className="text-2xl font-bold">Reports</h1>
    <form onSubmit={submit} className="grid md:grid-cols-4 gap-2 bg-white rounded-xl shadow p-4">
      <select className="border p-2 rounded" value={form.test_order_id} onChange={e => setForm({ ...form, test_order_id: e.target.value })} required>
        <option value="">Test Order</option>
        {testOrders.map(o => <option key={o.id} value={o.id}>Order #{o.id} - Patient {o.patient_id}</option>)}
      </select>
      <select className="border p-2 rounded" value={form.type} onChange={e => setForm({ ...form, type: e.target.value })}>
        <option value="hematology">Hematology</option>
        <option value="biochemistry">Biochemistry</option>
        <option value="serology">Serology</option>
        <option value="radiology">Radiology</option>
        <option value="histopathology">Histopathology</option>
      </select>
      <select className="border p-2 rounded" value={form.status} onChange={e => setForm({ ...form, status: e.target.value })}>
        <option value="processing">Processing</option>
        <option value="pending_approval">Pending Approval</option>
        <option value="approved">Approved</option>
        <option value="delivered">Delivered</option>
      </select>
      <input className="border p-2 rounded" placeholder="Digital Signature" value={form.digital_signature} onChange={e => setForm({ ...form, digital_signature: e.target.value })} />
      <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update Report' : 'Create Report'}</button>
      {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
    </form>

    <div className="bg-white rounded-xl shadow overflow-auto">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-slate-100">
            <th className="p-2 text-left">ID</th>
            <th className="p-2 text-left">Type</th>
            <th className="p-2 text-left">Status</th>
            <th className="p-2 text-left">Test Order</th>
            <th className="p-2 text-left">QR</th>
            <th className="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          {items.map(r => (
            <tr key={r.id} className="border-t">
              <td className="p-2">{r.id}</td>
              <td className="p-2">{r.type}</td>
              <td className="p-2">{r.status}</td>
              <td className="p-2">#{r.test_order_id}</td>
              <td className="p-2">{r.qr_code ? r.qr_code.slice(0, 8) + '...' : '-'}</td>
              <td className="p-2 space-x-2">
                <button className="text-blue-700" onClick={() => edit(r)}>Edit</button>
                <button className="text-red-700" onClick={() => remove(r.id)}>Delete</button>
                <button className="text-emerald-700" onClick={() => setStatus(r.id, 'approved')}>Approve</button>
                <button className="text-violet-700" onClick={() => generatePdf(r.id)}>PDF</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  </div>;
}
