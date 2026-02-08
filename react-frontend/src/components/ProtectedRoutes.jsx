import { Navigate } from "react-router-dom";
import { isLoggedIn, getUloga } from "../utils/auth";

export default function ProtectedRoute({ allowed, children }) {
  if (!isLoggedIn()) return <Navigate to="/" replace />;

  if (allowed && !allowed.includes(getUloga())) {
    return <Navigate to="/pocetna" replace />;
  }

  return children;
}