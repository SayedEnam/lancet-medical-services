import React from 'react';
import { Navigate, Route, Routes, Link, useNavigate } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';
import LoginPage from './pages/LoginPage';
import DashboardPage from './pages/DashboardPage';
import PatientsPage from './pages/PatientsPage';
import DoctorsPage from './pages/DoctorsPage';
import AppointmentsPage from './pages/AppointmentsPage';
import TestsPage from './pages/TestsPage';
import ReportsPage from './pages/ReportsPage';
import BillingPage from './pages/BillingPage';
import AccountingPage from './pages/AccountingPage';
import StaffPage from './pages/StaffPage';
import InventoryPage from './pages/InventoryPage';
import SettingsPage from './pages/SettingsPage';
import { ToastProvider } from './components/ToastProvider';
import api from './api';
import { logout } from './store/authSlice';
import { useBranding } from './hooks/useBranding';

const Protected = ({ children }) => {
  const token = useSelector((s) => s.auth.token);
  return token ? children : <Navigate to="/login" replace />;
};

const Layout = ({ children }) => {
  const dispatch = useDispatch();
  const navigate = useNavigate();
  const branding = useBranding();

  const handleLogout = async () => {
    try {
      await api.post('/auth/logout');
    } finally {
      dispatch(logout());
      navigate('/login', { replace: true });
    }
  };

  return (
    <div className="min-h-screen">
      <nav className="bg-slate-900 text-white p-4 flex gap-4 text-sm flex-wrap items-center">
        <Link to="/" className="mr-2 flex items-center gap-2 font-semibold">
          {branding.logo_url && <img src={branding.logo_url} alt={branding.center_name} className="h-8 w-8 rounded bg-white object-contain" />}
          <span>{branding.center_name}</span>
        </Link>
        <Link to="/">Dashboard</Link>
        <Link to="/appointments">Appointments</Link>
        <Link to="/patients">Patients</Link>
        <Link to="/doctors">Doctors</Link>
        <Link to="/tests">Tests</Link>
        <Link to="/reports">Reports</Link>
        <Link to="/billing">Billing</Link>
        <Link to="/accounting">Accounting</Link>
        <Link to="/staff">Staff</Link>
        <Link to="/inventory">Inventory</Link>
        <Link to="/settings">Settings</Link>
        <button
          type="button"
          onClick={handleLogout}
          className="ml-auto rounded bg-red-600 px-3 py-1.5 text-white hover:bg-red-700"
        >
          Logout
        </button>
      </nav>
      <main className="p-4">{children}</main>
    </div>
  );
};

const Guarded = ({ element }) => <Protected><Layout>{element}</Layout></Protected>;

export const AppRouter = () => (
  <ToastProvider>
    <Routes>
      <Route path="/login" element={<LoginPage />} />
      <Route path="/" element={<Guarded element={<DashboardPage />} />} />
      <Route path="/appointments" element={<Guarded element={<AppointmentsPage />} />} />
      <Route path="/patients" element={<Guarded element={<PatientsPage />} />} />
      <Route path="/doctors" element={<Guarded element={<DoctorsPage />} />} />
      <Route path="/tests" element={<Guarded element={<TestsPage />} />} />
      <Route path="/reports" element={<Guarded element={<ReportsPage />} />} />
      <Route path="/billing" element={<Guarded element={<BillingPage />} />} />
      <Route path="/accounting" element={<Guarded element={<AccountingPage />} />} />
      <Route path="/staff" element={<Guarded element={<StaffPage />} />} />
      <Route path="/inventory" element={<Guarded element={<InventoryPage />} />} />
      <Route path="/settings" element={<Guarded element={<SettingsPage />} />} />
    </Routes>
  </ToastProvider>
);
