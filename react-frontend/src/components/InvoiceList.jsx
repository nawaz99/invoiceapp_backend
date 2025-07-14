import React, { useEffect, useState } from "react";
import API from "../services/api";
import { Link } from "react-router-dom";

const InvoiceList = () => {
    const [invoices, setInvoices] = useState([]);

    useEffect(() => {
        API.get("/invoices")
            .then((res) => setInvoices(res.data))
            .catch((err) => console.error("Error loading invoices:", err));
    }, []);

    return (
        <div className="max-w-6xl mx-auto mt-10 p-4 bg-white rounded shadow">
            <h2 className="text-2xl font-bold mb-6 text-gray-700">Invoices</h2>
            <div className="overflow-x-auto">
                <table className="min-w-full text-sm text-left border border-gray-200">
                    <thead className="bg-gray-100 text-gray-700">
                        <tr>
                            <th className="px-4 py-2 border">ID</th>
                            <th className="px-4 py-2 border">Invoice #</th>
                            <th className="px-4 py-2 border">Client ID</th>
                            <th className="px-4 py-2 border">Date</th>
                            <th className="px-4 py-2 border">Due</th>
                            <th className="px-4 py-2 border">Total</th>
                            <th className="px-4 py-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        {invoices.map((inv) => (
                            <tr
                                key={inv.id}
                                className="hover:bg-gray-50 transition-colors"
                            >
                                <td className="px-4 py-2 border">{inv.id}</td>
                                <td className="px-4 py-2 border text-blue-600">
                                    <Link
                                        to={`/invoices/${inv.id}`}
                                        className="hover:underline"
                                    >
                                        {inv.invoice_number}
                                    </Link>
                                </td>
                                <td className="px-4 py-2 border">
                                    {inv.client_id}
                                </td>
                                <td className="px-4 py-2 border">
                                    {inv.invoice_date}
                                </td>
                                <td className="px-4 py-2 border">
                                    {inv.due_date}
                                </td>
                                <td className="px-4 py-2 border">
                                 ₹{Number(inv.total).toFixed(2)}
                                </td>
                                <td className="px-4 py-2 border">
                                    <span
                                        className={`px-2 py-1 rounded text-xs font-semibold ${
                                            inv.status === "paid"
                                                ? "bg-green-100 text-green-800"
                                                : inv.status === "overdue"
                                                ? "bg-red-100 text-red-800"
                                                : "bg-yellow-100 text-yellow-800"
                                        }`}
                                    >
                                        {inv.status}
                                    </span>
                                </td>
                            </tr>
                        ))}
                        {invoices.length === 0 && (
                            <tr>
                                <td
                                    colSpan="7"
                                    className="text-center text-gray-500 py-4"
                                >
                                    No invoices found.
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default InvoiceList;
