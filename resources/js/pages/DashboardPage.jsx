import React, { useEffect, useState } from 'react';
import api from '../api';

export default function DashboardPage() {
  const [data, setData] = useState(null);
  useEffect(() => { api.get('/dashboard').then(r => setData(r.data)); }, []);
  if (!data) return <p>Loading...</p>;

  const cards = [
    ['Today Appointments', data.today_appointments],
    ['Pending Reports', data.pending_reports],
    ['Total Patients', data.total_patients],
    ['Total Revenue', data.total_revenue],
    ['Due Payments', data.due_payments],
  ];

  return <div className="space-y-4">
    <h1 className="text-2xl font-bold">Diagnostic Center Dashboard</h1>
    <div className="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
      {cards.map(([label, value]) => <div key={label} className="bg-white p-4 rounded-xl shadow"><p className="text-sm text-slate-500">{label}</p><p className="text-xl font-semibold">{value}</p></div>)}
    </div>
  </div>;
}
