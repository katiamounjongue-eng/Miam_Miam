import './App.css';
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AccueilAdmin from "./pages/accueil_admin.jsx";
import AdminHome from "./pages/admin_home.jsx";
import OrderHistory from './pages/order_history.jsx';
import AdminStatistics from "./pages/admin_statistics.jsx";
import MessagesAdmin from './pages/messages_admin.jsx';
import Products from './pages/products.jsx';
import Add_products from './pages/add_products.jsx';
import Settings from './pages/settings.jsx';
import AccountManagement from './pages/account_management.jsx';


function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<AccueilAdmin />} />
        <Route path="/admin" element={<AdminHome />} />
        <Route path="/order_history" element={<OrderHistory />} />
        <Route path="/admin_statistics" element={<AdminStatistics />} />
        <Route path="/messages_admin" element={<MessagesAdmin/>}/>
        <Route path="/products" element={<Products/>}/>
        <Route path="/add_products" element={<Add_products/>}/>
        <Route path="/settings" element={<Settings/>}/>
        <Route path="/account_management" element={<AccountManagement/>}/>
      </Routes>
    </Router>
  );
}

export default App;

