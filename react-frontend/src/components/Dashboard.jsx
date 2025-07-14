import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import API from "../services/api";
import InvoiceList from "../components/InvoiceList";
import {
    BarChart,
    Bar,
    XAxis,
    YAxis,
    Tooltip,
    ResponsiveContainer,
} from "recharts";

const Dashboard = ({ onLogout }) => {
    const navigate = useNavigate();
    const [invoices, setInvoices] = useState([]);
    const [chartData, setChartData] = useState([]);

    useEffect(() => {
        API.get("/invoices")
            .then((res) => {
                setInvoices(res.data);

                const monthlyTotals = {};
                res.data.forEach((inv) => {
                    const month = new Date(inv.invoice_date).toLocaleString(
                        "default",
                        { month: "short" }
                    );
                    monthlyTotals[month] =
                        (monthlyTotals[month] || 0) + parseFloat(inv.total);
                });

                const chart = Object.keys(monthlyTotals).map((month) => ({
                    month,
                    total: monthlyTotals[month],
                }));

                setChartData(chart);
            })
            .catch((err) => console.error(err));
    }, []);

    const totalEarnings = invoices.reduce(
        (sum, inv) => sum + parseFloat(inv.total),
        0
    );

    const logout = () => {
        localStorage.removeItem("invoice_token");
        onLogout(); // this triggers redirect logic in App
        navigate("/");
    };

    return (
        <div className="min-h-screen bg-gray-100 p-6">
            {/* Header */}
            <div className="flex justify-between items-center mb-6">
                <h1 className="text-3xl font-bold text-gray-800">Dashboard</h1>
                <button
                    onClick={logout}
                    className="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                >
                    Logout
                </button>
            </div>

            {/* Summary Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div className="bg-white p-6 rounded shadow">
                    <p className="text-gray-500">Total Invoices</p>
                    <h2 className="text-3xl font-bold text-gray-800">
                        {invoices.length}
                    </h2>
                </div>
                <div className="bg-white p-6 rounded shadow">
                    <p className="text-gray-500">Total Earnings</p>
                    <h2 className="text-3xl font-bold text-gray-800">
                        ₹ {totalEarnings.toFixed(2)}
                    </h2>
                </div>
            </div>

            {/* Chart */}
            <div className="bg-white p-6 rounded shadow mb-6">
                <h3 className="text-xl font-semibold mb-4">Monthly Earnings</h3>
                <ResponsiveContainer width="100%" height={250}>
                    <BarChart data={chartData}>
                        <XAxis dataKey="month" />
                        <YAxis />
                        <Tooltip />
                        <Bar dataKey="total" fill="#6366f1" />
                    </BarChart>
                </ResponsiveContainer>
            </div>

            {/* Navigation Buttons */}
            <div className="flex gap-4 mb-6">
                <button
                    onClick={() => navigate("/clients")}
                    className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                >
                    👥 Manage Clients
                </button>
                <button
                    onClick={() => navigate("/invoices/create")}
                    className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
                >
                    ➕ Create Invoice
                </button>
            </div>

            {/* Invoice List */}
            <InvoiceList />
        </div>
    );
};

export default Dashboard;
