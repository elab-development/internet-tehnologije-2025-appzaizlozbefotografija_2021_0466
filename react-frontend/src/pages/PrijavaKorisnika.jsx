import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import axios from "axios";

import Button from "../components/Button";
import Input from "../components/Input";

export default function PrijavaKorisnika() {
  const navigate = useNavigate();

  const [email, setEmail] = useState("");
  const [lozinka, setLozinka] = useState("");
  const [greska, setGreska] = useState("");
  const [loading, setLoading] = useState(false);

  const submit = async (e) => {
    e.preventDefault();
    setGreska("");
    setLoading(true);

    try {
      const res = await axios.post("http://127.0.0.1:8000/api/prijava", {
        email,
        lozinka,
      });

      const { token, korisnik } = res.data;

      localStorage.setItem("token", token);
      localStorage.setItem("korisnikId", korisnik.id);

      navigate("/pocetna");
    } catch (err) {
      setGreska(
        err?.response?.data?.poruka || "Pogrešan email ili lozinka."
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ maxWidth: 400 }}>
      <h2>Prijava korisnika</h2>

      <form onSubmit={submit}>
        <Input
          label="Email"
          name="email"
          type="email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          
        />

        <Input
          label="Lozinka"
          name="lozinka"
          type="password"
          value={lozinka}
          onChange={(e) => setLozinka(e.target.value)}
          
        />

        <Button
          type="submit"
          disabled={loading}
        >
          {loading ? "Prijava..." : "Prijavi se"}
        </Button>

      </form>

      {greska && <p style={{ color: "red" }}>{greska}</p>}

      <p>
        Nemaš nalog? <Link to="/registracija">Registruj se</Link>
      </p>
    </div>
  );
}