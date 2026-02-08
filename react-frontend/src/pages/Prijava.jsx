import { useEffect, useState } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";
import axios from "axios";
import Button from "../components/Button";
import "./Prijava.css";

export default function Prijava() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [izlozba, setIzlozba] = useState(null);
  const [loading, setLoading] = useState(true);
  const [poruka, setPoruka] = useState("");
  const [greska, setGreska] = useState("");

  const token = localStorage.getItem("token");
  const korisnikId = localStorage.getItem("korisnikId");

  useEffect(() => {
    axios
      .get(`http://127.0.0.1:8000/api/izlozbe/${id}`)
      .then((res) => {
        const data = res.data?.data ?? res.data;
        setIzlozba(data);
      })
      .catch(() => setGreska("Ne mogu da učitam izložbu."))
      .finally(() => setLoading(false));
  }, [id]);

  const rezervisi = async () => {
    setPoruka("");
    setGreska("");

    if (!token) {
      setGreska("Moraš prvo da se prijaviš.");
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

      setPoruka(res.data?.poruka || "Uspešno rezervisano!");
    } catch (e) {
      const msg =
        e?.response?.data?.poruka ||
        e?.response?.data?.message ||
        "Greška pri rezervaciji.";
      setGreska(msg);
    }
  };

  if (loading) return <div className="prj-state">Učitavanje...</div>;
  if (!izlozba) return <div className="prj-state prj-state--error">Izložba ne postoji.</div>;

  return (
    <div className="prj-page">
      <div className="prj-container">
        <div className="prj-card">
          <div className="prj-top">
            <Link className="prj-back" to={`/izlozbe/${id}`}>
              ← Nazad na izložbu
            </Link>
          </div>

          <h2 className="prj-title">Rezervacija mesta</h2>

          <p className="prj-subtitle">
            Rezervišeš mesto za izložbu <b>{izlozba.naziv}</b>.
          </p>

          <div className="prj-meta">
            <div className="prj-metaItem">
              <span className="prj-label">Lokacija</span>
              <div className="prj-value">{izlozba.lokacija}</div>
            </div>

            <div className="prj-metaItem">
              <span className="prj-label">Datum</span>
              <div className="prj-value">{izlozba.datum}</div>
            </div>
          </div>

          <div className="prj-actions">
            <Button onClick={rezervisi}>Rezerviši mesto</Button>

            <button
              className="prj-secondaryBtn"
              type="button"
              onClick={() => navigate(`/izlozbe/${id}/galerija`)}
            >
              Pogledaj galeriju
            </button>
          </div>

          {poruka && <div className="prj-alert prj-alert--ok">{poruka}</div>}
          {greska && <div className="prj-alert prj-alert--err">{greska}</div>}

          {!token && (
            <div className="prj-hint">
              Nisi prijavljen. Vrati se na login stranicu i prijavi se.
            </div>
          )}
        </div>
      </div>
    </div>
  );
}