import React from 'react';
import { Link, Outlet, useNavigate } from 'react-router-dom';

const Layout = ({ onLogout }) => {
  const navigate = useNavigate();

  const logout = () => {
    localStorage.removeItem('invoice_token');
    onLogout();
    navigate('/');
  };

  return (
    <div className="flex min-h-screen">
      {/* Sidebar */}
      <aside className="w-64 bg-gray-900 text-white p-6 space-y-4">
        <h2 className="text-xl font-bold mb-6">Invoice App</h2>
        <nav className="flex flex-col space-y-3 text-sm">
          <Link to="/dashboard" className="hover:underline">🏠 Dashboard</Link>
          <Link to="/invoices/create" className="hover:underline">➕ Create Invoice</Link>
          <Link to="/clients" className="hover:underline">👥 Clients</Link>
          <Link to="/clients/create" className="hover:underline">➕ Add Client</Link>
          <button onClick={logout} className="text-red-300 hover:underline text-left">🚪 Logout</button>
        </nav>
      </aside>

      {/* Page Content */}
      <main className="flex-1 p-6 bg-gray-50 overflow-y-auto">
        <Outlet />
      </main>
    </div>
  );
};

export default Layout;
