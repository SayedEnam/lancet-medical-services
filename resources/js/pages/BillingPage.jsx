import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function BillingPage() {
  const { showToast } = useToast();

  const emptyInvoice = { patient_id: '', sub_total: 0, discount: 0, vat: 0, paid_amount: 0 };
  const emptyPayment = { invoice_id: '', amount: '', method: 'cash', transaction_no: '' };

  const [patients, setPatients] = useState([]);
  const [invoices, setInvoices] = useState([]);
  const [invoiceForm, setInvoiceForm] = useState(emptyInvoice);
  const [paymentForm, setPaymentForm] = useState(emptyPayment);

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const [pRes, iRes] = await Promise.all([api.get('/patients'), api.get('/invoices')]);
      setPatients(pRes.data.data || []);
      setInvoices(iRes.data.data || []);
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  useEffect(() => { load(); }, []);

  const createInvoice = async (e) => {
    e.preventDefault();
    try {
      await api.post('/invoices', invoiceForm);
      showToast('Invoice created');
      setInvoiceForm(emptyInvoice);
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const addPayment = async (e) => {
    e.preventDefault();
    try {
      await api.post('/payments', paymentForm);
      showToast('Payment added');
      setPaymentForm(emptyPayment);
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const deleteInvoice = async (id) => {
    if (!confirm('Delete this invoice?')) return;
    try {
      await api.delete(`/invoices/${id}`);
      showToast('Invoice deleted');
      load();
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const totalPreview = Math.max(0, Number(invoiceForm.sub_total || 0) - Number(invoiceForm.discount || 0) + Number(invoiceForm.vat || 0));
  const duePreview = Math.max(0, totalPreview - Number(invoiceForm.paid_amount || 0));

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Billing</h1>

    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Create Invoice</h2>
      <form onSubmit={createInvoice} className="grid md:grid-cols-3 gap-2">
        <select className="border p-2 rounded" value={invoiceForm.patient_id} onChange={e=>setInvoiceForm({...invoiceForm, patient_id:e.target.value})} required>
          <option value="">Patient</option>
          {patients.map(p => <option key={p.id} value={p.id}>{p.name ? `${p.name} (${p.patient_id})` : p.patient_id}</option>)}
        </select>
        <input type="number" className="border p-2 rounded" placeholder="Sub Total" value={invoiceForm.sub_total} onChange={e=>setInvoiceForm({...invoiceForm, sub_total:e.target.value})} required />
        <input type="number" className="border p-2 rounded" placeholder="Discount" value={invoiceForm.discount} onChange={e=>setInvoiceForm({...invoiceForm, discount:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="VAT" value={invoiceForm.vat} onChange={e=>setInvoiceForm({...invoiceForm, vat:e.target.value})} />
        <input type="number" className="border p-2 rounded" placeholder="Paid Now" value={invoiceForm.paid_amount} onChange={e=>setInvoiceForm({...invoiceForm, paid_amount:e.target.value})} />
        <div className="text-sm text-slate-600 p-2">Total: {totalPreview.toFixed(2)} | Due: {duePreview.toFixed(2)}</div>
        <button className="bg-slate-900 text-white p-2 rounded">Create Invoice</button>
      </form>
    </section>

    <section className="bg-white rounded-xl shadow p-4 space-y-3">
      <h2 className="text-lg font-semibold">Collect Payment</h2>
      <form onSubmit={addPayment} className="grid md:grid-cols-4 gap-2">
        <select className="border p-2 rounded" value={paymentForm.invoice_id} onChange={e=>setPaymentForm({...paymentForm, invoice_id:e.target.value})} required>
          <option value="">Invoice</option>
          {invoices.map(i => <option key={i.id} value={i.id}>{i.invoice_no} (Due: {i.due_amount})</option>)}
        </select>
        <input type="number" className="border p-2 rounded" placeholder="Amount" value={paymentForm.amount} onChange={e=>setPaymentForm({...paymentForm, amount:e.target.value})} required />
        <select className="border p-2 rounded" value={paymentForm.method} onChange={e=>setPaymentForm({...paymentForm, method:e.target.value})}>
          <option value="cash">Cash</option><option value="card">Card</option><option value="bkash">bKash</option><option value="nagad">Nagad</option><option value="rocket">Rocket</option><option value="bank_transfer">Bank Transfer</option>
        </select>
        <input className="border p-2 rounded" placeholder="Transaction No" value={paymentForm.transaction_no} onChange={e=>setPaymentForm({...paymentForm, transaction_no:e.target.value})} />
        <button className="bg-slate-900 text-white p-2 rounded">Add Payment</button>
      </form>
    </section>

    <section className="bg-white rounded-xl shadow overflow-auto">
      <table className="w-full text-sm">
        <thead>
          <tr className="bg-slate-100">
            <th className="p-2 text-left">Invoice</th>
            <th className="p-2 text-left">Patient</th>
            <th className="p-2 text-left">Total</th>
            <th className="p-2 text-left">Paid</th>
            <th className="p-2 text-left">Due</th>
            <th className="p-2 text-left">Status</th>
            <th className="p-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          {invoices.map(i => (
            <tr key={i.id} className="border-t">
              <td className="p-2">{i.invoice_no}</td>
              <td className="p-2">{i.patient?.name || `Patient #${i.patient_id}`}</td>
              <td className="p-2">{Number(i.total_amount).toFixed(2)}</td>
              <td className="p-2">{Number(i.paid_amount).toFixed(2)}</td>
              <td className="p-2">{Number(i.due_amount).toFixed(2)}</td>
              <td className="p-2">{i.status}</td>
              <td className="p-2"><button className="text-red-700" onClick={() => deleteInvoice(i.id)}>Delete</button></td>
            </tr>
          ))}
        </tbody>
      </table>
    </section>
  </div>;
}
