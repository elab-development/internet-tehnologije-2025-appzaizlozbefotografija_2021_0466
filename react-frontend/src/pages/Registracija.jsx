import { useState } from "react";
import { useNavigate, Link } from "react-router-dom";
import axios from "axios";
import Input from "../components/Input";
import Button from "../components/Button";

export default function Registracija() {
  const navigate = useNavigate();

  const [ime, setIme] = useState("");
  const [prezime, setPrezime] = useState("");
  const [email, setEmail] = useState("");
  const [lozinka, setLozinka] = useState("");
  const [lozinkaPotvrda, setLozinkaPotvrda] = useState("");

  const [loading, setLoading] = useState(false);
  const [poruka, setPoruka] = useState("");
  const [greska, setGreska] = useState("");

  const submit = async (e) => {
    e.preventDefault();
    setPoruka("");
    setGreska("");
    setLoading(true);

    try {
      const res = await axios.post("http://127.0.0.1:8000/api/registracija", {
        ime,
        prezime,
        email,
        lozinka,
        lozinka_confirmation: lozinkaPotvrda,
      });

      // očekujemo: { korisnik: {...}, token: "..." }
      const token = res.data.token;
      const korisnik = res.data.korisnik;

      if (!token || !korisnik?.id) {
        setGreska("Registracija je prošla, ali nema tokena/korisnika u odgovoru.");
        return;
      }

      localStorage.setItem("token", token);
      localStorage.setItem("korisnikId", String(korisnik.id));

      setPoruka("Uspešna registracija! Token je sačuvan.");
      navigate("/");
    } catch (err) {
      const msg =
        err?.response?.data?.message ||
        err?.response?.data?.poruka ||
        "Greška pri registraciji.";
      setGreska(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div style={{ maxWidth: 420 }}>
      <h2>Registracija</h2>

      <form onSubmit={submit}>
        <Input
          label="Ime"
          name="ime"
          value={ime}
          onChange={(e) => setIme(e.target.value)}
          placeholder="Unesite ime"
        />

        <Input
          label="Prezime"
          name="prezime"
          value={prezime}
          onChange={(e) => setPrezime(e.target.value)}
        />

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

        <Input
          label="Potvrda lozinke"
          name="lozinkaPotvrda"
          type="password"
          value={lozinkaPotvrda}
          onChange={(e) => setLozinkaPotvrda(e.target.value)}
        />

        <Button type="submit" disabled={loading}>
          {loading ? "Slanje..." : "Registruj se"}
        </Button>
      </form>

      {poruka && <p style={{ color: "green" }}>{poruka}</p>}
      {greska && <p style={{ color: "red" }}>{greska}</p>}

      <p style={{ marginTop: 12 }}>
        <Link to="/">Nazad na izložbe</Link>
      </p>
    </div>
  );
}