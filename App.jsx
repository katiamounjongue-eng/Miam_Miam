// src/App.jsx
import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import Sidebar from './components/Sidebar';
import Header from './components/Header'; // Header intégré
import OrderHistory from './pages/OrderHistory';
import Messages from './pages/Messages';
import Statistics from './pages/Statistics';
import AccountManagement from './pages/AccountManagement';
import './App.css'; // style global

function App() {
  return (
    <div className="app-container">
      <Sidebar />
      <Header />

      <main className="app-main">
        <Routes>
          <Route path="/order-history" element={<OrderHistory />} />
          <Route path="/messages" element={<Messages />} />
          <Route path="/statistics" element={<Statistics />} />
          <Route path="/accounts" element={<AccountManagement />} />
          <Route path="*" element={<Navigate to="/accounts" replace />} />
        </Routes>
      </main>
    </div>
  );
}

export default App;
