import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function AccountingPage() {
  const { showToast } = useToast();
  const empty = { title: '', category: '', amount: '', expense_date: '', notes: '' };

  const [summary, setSummary] = useState(null);
  const [expenses, setExpenses] = useState([]);
  const [editingId, setEditingId] = useState(null);
  const [form, setForm] = useState(empty);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [sumRes, expRes] = await Promise.all([
        api.get('/accounting/summary'),
        api.get('/expenses'),
      ]);
      setSummary(sumRes.data);
      setExpenses(expRes.data.data || []);
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  useEffect(() => { load(); }, []);

  const submit = async (e) => {
    e.preventDefault();
    try {
      if (editingId) {
        await api.put(`/expenses/${editingId}`, form);
        showToast('Expense updated');
      } else {
        await api.post('/expenses', form);
        showToast('Expense added');
      }
      setForm(empty);
      setEditingId(null);
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const edit = (item) => {
    setEditingId(item.id);
    setForm({
      title: item.title || '',
      category: item.category || '',
      amount: item.amount || '',
      expense_date: item.expense_date || '',
      notes: item.notes || '',
    });
  };

  const remove = async (id) => {
    if (!confirm('Delete this expense?')) return;
    try {
      await api.delete(`/expenses/${id}`);
      showToast('Expense deleted');
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const card = (title, value) => (
    <div className="bg-white rounded-xl shadow p-4">
      <p className="text-sm text-slate-500">{title}</p>
      <p className="text-2xl font-semibold">{Number(value || 0).toFixed(2)}</p>
    </div>
  );

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Accounting</h1>

    <div className="grid md:grid-cols-3 gap-3">
      {card('Income Today', summary?.income_today)}
      {card('Expense Today', summary?.expense_today)}
      {card('Net Today', summary?.net_today)}
      {card('Income Month', summary?.income_month)}
      {card('Expense Month', summary?.expense_month)}
      {card('Net Month', summary?.net_month)}
    </div>

    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Expense Entry</h2>
      <form onSubmit={submit} className="grid md:grid-cols-3 gap-2">
        <input className="border p-2 rounded" placeholder="Title" value={form.title} onChange={e=>setForm({...form, title:e.target.value})} required />
        <input className="border p-2 rounded" placeholder="Category" value={form.category} onChange={e=>setForm({...form, category:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="Amount" value={form.amount} onChange={e=>setForm({...form, amount:e.target.value})} required />
        <input type="date" className="border p-2 rounded" value={form.expense_date} onChange={e=>setForm({...form, expense_date:e.target.value})} required />
        <input className="border p-2 rounded md:col-span-2" placeholder="Notes" value={form.notes} onChange={e=>setForm({...form, notes:e.target.value})} />
        <button className="bg-slate-900 text-white p-2 rounded">{editingId ? 'Update Expense' : 'Add Expense'}</button>
        {editingId && <button type="button" className="bg-slate-300 p-2 rounded" onClick={() => { setEditingId(null); setForm(empty); }}>Cancel</button>}
      </form>
    </section>

    <section className="bg-white rounded-xl shadow overflow-auto">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-slate-100">
            <th className="p-2 text-left">Date</th>
            <th className="p-2 text-left">Title</th>
            <th className="p-2 text-left">Category</th>
            <th className="p-2 text-left">Amount</th>
            <th className="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          {expenses.map(e => (
            <tr key={e.id} className="border-t">
              <td className="p-2">{e.expense_date}</td>
              <td className="p-2">{e.title}</td>
              <td className="p-2">{e.category}</td>
              <td className="p-2">{Number(e.amount).toFixed(2)}</td>
              <td className="p-2 space-x-2">
                <button className="text-blue-700" onClick={() => edit(e)}>Edit</button>
                <button className="text-red-700" onClick={() => remove(e.id)}>Delete</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </section>
  </div>;
}
