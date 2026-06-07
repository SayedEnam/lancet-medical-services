import React, { useEffect, useState } from 'react';
import api from '../api';
import { useToast } from '../components/ToastProvider';

export default function SettingsPage() {
  const { showToast } = useToast();
  const [logoFile, setLogoFile] = useState(null);

  const [form, setForm] = useState({
    center_name: '',
    logo_url: '',
    center_phone: '',
    center_email: '',
    center_address: '',
    report_header: '',
    report_footer: '',
    sms_api_endpoint: '',
    whatsapp_api_endpoint: '',
    whatsapp_api_token: '',
    sms_api_token: '',
  });

  const err = (e) => e?.response?.data?.message || 'Operation failed';

  const load = async () => {
    try {
      const { data } = await api.get('/settings', { params: { group: 'general' } });
      if (data?.settings) {
        setForm((prev) => ({ ...prev, ...data.settings }));
      }
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  useEffect(() => { load(); }, []);

  const save = async (e) => {
    e.preventDefault();
    try {
      await api.post('/settings', {
        group: 'general',
        settings: form,
      });
      showToast('Settings saved');
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  const uploadLogo = async () => {
    if (!logoFile) {
      showToast('Please choose a logo file first', 'error');
      return;
    }

    const payload = new FormData();
    payload.append('logo', logoFile);

    try {
      const { data } = await api.post('/settings/logo', payload, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      setForm((prev) => ({ ...prev, logo_url: data.logo_url }));
      setLogoFile(null);
      showToast('Logo uploaded');
    } catch (e) {
      showToast(err(e), 'error');
    }
  };

  return <div className="space-y-6">
    <h1 className="text-2xl font-bold">Settings</h1>

    <form onSubmit={save} className="bg-white rounded-xl shadow p-4 grid md:grid-cols-2 gap-3">
      <h2 className="md:col-span-2 text-lg font-semibold">Diagnostic Center Profile</h2>
      <div className="md:col-span-2 flex flex-wrap items-center gap-3">
        {form.logo_url && <img src={form.logo_url} alt="Center logo" className="h-16 w-16 rounded border object-contain" />}
        <input type="file" accept="image/*" className="border p-2 rounded" onChange={e=>setLogoFile(e.target.files?.[0] || null)} />
        <button type="button" className="bg-slate-700 text-white px-4 py-2 rounded" onClick={uploadLogo}>Upload Logo</button>
      </div>
      <input className="border p-2 rounded" placeholder="Center Name" value={form.center_name} onChange={e=>setForm({...form, center_name:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Center Phone" value={form.center_phone} onChange={e=>setForm({...form, center_phone:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Center Email" value={form.center_email} onChange={e=>setForm({...form, center_email:e.target.value})} />
      <input className="border p-2 rounded" placeholder="Center Address" value={form.center_address} onChange={e=>setForm({...form, center_address:e.target.value})} />

      <h2 className="md:col-span-2 text-lg font-semibold mt-2">Report Branding</h2>
      <input className="border p-2 rounded md:col-span-2" placeholder="Report Header Text" value={form.report_header} onChange={e=>setForm({...form, report_header:e.target.value})} />
      <input className="border p-2 rounded md:col-span-2" placeholder="Report Footer Text" value={form.report_footer} onChange={e=>setForm({...form, report_footer:e.target.value})} />

      <h2 className="md:col-span-2 text-lg font-semibold mt-2">Notification Gateways</h2>
      <input className="border p-2 rounded" placeholder="SMS API Endpoint" value={form.sms_api_endpoint} onChange={e=>setForm({...form, sms_api_endpoint:e.target.value})} />
      <input className="border p-2 rounded" placeholder="SMS API Token" value={form.sms_api_token} onChange={e=>setForm({...form, sms_api_token:e.target.value})} />
      <input className="border p-2 rounded" placeholder="WhatsApp API Endpoint" value={form.whatsapp_api_endpoint} onChange={e=>setForm({...form, whatsapp_api_endpoint:e.target.value})} />
      <input className="border p-2 rounded" placeholder="WhatsApp API Token" value={form.whatsapp_api_token} onChange={e=>setForm({...form, whatsapp_api_token:e.target.value})} />

      <div className="md:col-span-2">
        <button className="bg-slate-900 text-white px-4 py-2 rounded">Save Settings</button>
      </div>
    </form>
  </div>;
}
