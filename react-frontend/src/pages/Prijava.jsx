import { useEffect, useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import axios from "axios";
import Button from "../components/Button";

export default function Prijava() {
  const { id } = useParams(); // id izložbe
  const navigate = useNavigate();

  const [izlozba, setIzlozba] = useState(null);
  const [loading, setLoading] = useState(true);
  const [poruka, setPoruka] = useState("");
  const [greska, setGreska] = useState("");

  // token i korisnik (ako si ih sačuvao posle login/registracija)
  const token = localStorage.getItem("token");
  const korisnikId = localStorage.getItem("korisnikId");

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/izlozbe/${id}`)
      .then((res) => setIzlozba(res.data))
      .catch(() => setGreska("Ne mogu da učitam izložbu."))
      .finally(() => setLoading(false));
  }, [id]);

  const rezervisi = async () => {
    setPoruka("");
    setGreska("");

    if (!token) {
      setGreska("Moraš prvo da se prijaviš (nema tokena).");
      return;
    }
    if (!korisnikId) {
      setGreska("Nema korisnikId u localStorage (uradi login/registraciju ponovo).");
      return;
    }

    try {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/prijave",
        {
          korisnik_id: Number(korisnikId),
          izlozba_id: Number(id),
        },
        {
          headers: {
            Authorization: `Bearer ${token}`,
            Accept: "application/json",
          },
        }
      );

      setPoruka(res.data.poruka || "Uspešno rezervisano!");
    } catch (e) {
      const msg =
        e?.response?.data?.poruka ||
        e?.response?.data?.message ||
        "Greška pri rezervaciji.";
      setGreska(msg);
    }
  };

  if (loading) return <p>Učitavanje...</p>;
  if (!izlozba) return <p style={{ color: "red" }}>Izložba ne postoji.</p>;

  return (
    <div>
      <Link to={`/izlozbe/${id}`}>← Nazad na izložbu</Link>

      <h2>Prijava na izložbu</h2>
      <p>
        <b>{izlozba.naziv}</b> – {izlozba.lokacija} ({izlozba.datum})
      </p>

      <Button onClick={rezervisi}>
        Rezerviši mesto
      </Button>

      {poruka && <p style={{ color: "green", marginTop: 12 }}>{poruka}</p>}
      {greska && <p style={{ color: "red", marginTop: 12 }}>{greska}</p>}

      <div style={{ marginTop: 16 }}>
        <Button onClick={() => navigate(`/izlozbe/${id}/galerija`)}>
          Pogledaj galeriju
        </Button>
      </div>
    </div>
  );
}