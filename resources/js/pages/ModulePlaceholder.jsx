import React from 'react';

export default function ModulePlaceholder({ title }) {
  return (
    <div className="bg-white rounded-xl shadow p-6">
      <h2 className="text-2xl font-bold">{title}</h2>
      <p className="text-slate-600 mt-2">This module is ready for full implementation.</p>
    </div>
  );
}
