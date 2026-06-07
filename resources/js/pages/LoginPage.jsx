import React, { useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import api from '../api';
import { setAuth } from '../store/authSlice';
import { useBranding } from '../hooks/useBranding';

export default function LoginPage() {
  const branding = useBranding();
  const [email, setEmail] = useState('admin@lancet.com');
  const [password, setPassword] = useState('password');
  const [error, setError] = useState('');
  const dispatch = useDispatch();
  const navigate = useNavigate();

  const submit = async (e) => {
    e.preventDefault();
    try {
      const { data } = await api.post('/auth/login', { email, password });
      dispatch(setAuth(data));
      navigate('/');
    } catch {
      setError('Login failed');
    }
  };

  return <div className="min-h-screen grid place-items-center">
    <form onSubmit={submit} className="bg-white p-6 rounded-xl shadow w-full max-w-md space-y-3">
      <div className="flex justify-center">
        {branding.logo_url ? (
          <img src={branding.logo_url} alt={branding.center_name} className="h-20 max-w-56 rounded object-contain" />
        ) : (
          <h1 className="text-center text-2xl font-bold">{branding.center_name}</h1>
        )}
      </div>
      <input className="w-full border p-2 rounded" value={email} onChange={(e)=>setEmail(e.target.value)} />
      <input className="w-full border p-2 rounded" type="password" value={password} onChange={(e)=>setPassword(e.target.value)} />
      {error && <p className="text-red-600 text-sm">{error}</p>}
      <button className="w-full bg-slate-900 text-white p-2 rounded">Login</button>
    </form>
  </div>;
}
