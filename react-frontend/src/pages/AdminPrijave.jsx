import { useEffect, useMemo, useState } from "react";
import axios from "axios";
import "./AdminPanel.css";

const API = "http://127.0.0.1:8000/api";

export default function AdminPrijave() {
  const token = localStorage.getItem("token");

  const [prijave, setPrijave] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");
  const [poruka, setPoruka] = useState("");

  const headers = useMemo(
    () => ({
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    }),
    [token]
  );

  const ucitaj = () => {
    setLoading(true);
    setGreska("");
    setPoruka("");

    axios
      .get(`${API}/prijave`, { headers })
      .then((res) => {
        const data = res.data?.data ?? res.data;
        setPrijave(Array.isArray(data) ? data : []);
      })
      .catch((e) => {
        setGreska(
          e?.response?.data?.poruka ||
            e?.response?.data?.message ||
            "Greška pri učitavanju prijava."
        );
      })
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    ucitaj();
    
  }, []);

  const obrisi = async (id) => {
    setPoruka("");
    setGreska("");
    if (!window.confirm("Da li sigurno želiš da obrišeš prijavu?")) return;

    try {
      await axios.delete(`${API}/prijave/${id}`, { headers });
      setPoruka("Prijava je obrisana.");
      ucitaj();
    } catch (err) {
      setGreska(
        err?.response?.data?.poruka ||
          err?.response?.data?.message ||
          "Greška pri brisanju prijave."
      );
    }
  };

  const fmtDate = (s) => {
    if (!s) return "";
    return s.toString().slice(0, 10);
  };

  return (
    <div className="ap-page">
      <div className="ap-container">
        <h2 className="ap-title">Admin</h2>
        <p className="ap-subtitle">Pregled svih prijava</p>

        <div className="ap-card">
          <div className="ap-actions">
            <button className="ap-btn ap-btnGhost" type="button" onClick={ucitaj}>
              Osveži listu
            </button>
          </div>

          {poruka && <div className="ap-alert ap-ok">{poruka}</div>}
          {greska && <div className="ap-alert ap-err">{greska}</div>}

          {loading ? (
            <div className="ap-state">Učitavanje...</div>
          ) : prijave.length === 0 ? (
            <div className="ap-state">Nema prijava.</div>
          ) : (
            <div className="ap-table">
              {prijave.map((p) => {
                const nazivIzlozbe = p.izlozba?.naziv ?? `Izložba #${p.izlozba_id}`;
                const korisnikTxt =
                  p.korisnik?.email
                    ? p.korisnik.email
                    : `Korisnik #${p.korisnik_id}`;

                return (
                  <div className="ap-row" key={p.id}>
                    <div className="ap-rowMain">
                      <div className="ap-rowTitle">{nazivIzlozbe}</div>
                      <div className="ap-rowMeta">
                        {korisnikTxt}
                        {p.datum_prijave ? ` • datum prijave: ${fmtDate(p.datum_prijave)}` : ""}
                        {p.created_at ? ` • kreirano: ${fmtDate(p.created_at)}` : ""}
                      </div>
                      {p.qr_kod ? <div className="ap-rowMeta">QR: {p.qr_kod}</div> : null}
                    </div>

                    <div className="ap-rowActions">
                      <button
                        className="ap-btn ap-btnDanger"
                        onClick={() => obrisi(p.id)}
                        type="button"
                      >
                        Obriši
                      </button>
                    </div>
                  </div>
                );
              })}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}