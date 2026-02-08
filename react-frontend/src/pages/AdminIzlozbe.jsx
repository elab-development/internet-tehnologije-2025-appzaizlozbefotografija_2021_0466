import { useEffect, useMemo, useState } from "react";
import axios from "axios";
import "./AdminPanel.css";

const API = "http://127.0.0.1:8000/api";

export default function AdminIzlozbe() {
  const token = localStorage.getItem("token");

  const [izlozbe, setIzlozbe] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");
  const [poruka, setPoruka] = useState("");

  const [form, setForm] = useState({
    id: null,
    naziv: "",
    opis: "",
    datum: "",
    lokacija: "",
    dostupna_mesta: "",
  });

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
    axios
      .get(`${API}/izlozbe`, { headers })
      .then((res) => {
        const data = res.data?.data ?? res.data;
        setIzlozbe(Array.isArray(data) ? data : []);
      })
      .catch(() => setGreska("Greška pri učitavanju izložbi."))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    ucitaj();
   
  }, []);

  const onChange = (e) => {
    const { name, value } = e.target;
    setForm((p) => ({ ...p, [name]: value }));
  };

  const resetForm = () => {
    setForm({
      id: null,
      naziv: "",
      opis: "",
      datum: "",
      lokacija: "",
      dostupna_mesta: "",
    });
  };

  const submit = async (e) => {
    e.preventDefault();
    setPoruka("");
    setGreska("");

    try {
      const payload = {
        naziv: form.naziv,
        opis: form.opis,
        datum: form.datum,
        lokacija: form.lokacija,
        dostupna_mesta: Number(form.dostupna_mesta || 0),
      };

      if (form.id) {
       
        await axios.put(`${API}/izlozbe/${form.id}`, payload, { headers });
        setPoruka("Izložba je izmenjena.");
      } else {
       
        await axios.post(`${API}/izlozbe`, payload, { headers });
        setPoruka("Izložba je kreirana.");
      }

      resetForm();
      ucitaj();
    } catch (err) {
      setGreska(
        err?.response?.data?.poruka ||
          err?.response?.data?.message ||
          "Greška pri čuvanju izložbe."
      );
    }
  };

  const edit = (i) => {
    setPoruka("");
    setGreska("");
    setForm({
      id: i.id,
      naziv: i.naziv ?? "",
      opis: i.opis ?? "",
      datum: i.datum ?? "",
      lokacija: i.lokacija ?? "",
      dostupna_mesta: i.dostupna_mesta ?? "",
    });
    window.scrollTo({ top: 0, behavior: "smooth" });
  };

  const obrisi = async (id) => {
    setPoruka("");
    setGreska("");
    if (!window.confirm("Da li sigurno želiš da obrišeš izložbu?")) return;

    try {
      await axios.delete(`${API}/izlozbe/${id}`, { headers });
      setPoruka("Izložba je obrisana.");
      ucitaj();
    } catch (err) {
      setGreska(
        err?.response?.data?.poruka ||
          err?.response?.data?.message ||
          "Greška pri brisanju izložbe."
      );
    }
  };

  return (
    <div className="ap-page">
      <div className="ap-container">
        <h2 className="ap-title">Admin</h2>
        

        <div className="ap-card">
          <h3 className="ap-cardTitle">{form.id ? "Izmeni izložbu" : "Kreiraj izložbu"}</h3>

          <form className="ap-form" onSubmit={submit}>
            <div className="ap-grid2">
              <div className="ap-field">
                <label>Naziv</label>
                <input name="naziv" value={form.naziv} onChange={onChange} required />
              </div>
              <div className="ap-field">
                <label>Lokacija</label>
                <input name="lokacija" value={form.lokacija} onChange={onChange} required />
              </div>

              <div className="ap-field">
                <label>Datum</label>
                <input name="datum" value={form.datum} onChange={onChange} required />
              </div>
              <div className="ap-field">
                <label>Dostupna mesta</label>
                <input
                  name="dostupna_mesta"
                  value={form.dostupna_mesta}
                  onChange={onChange}
                  type="number"
                  min="0"
                  required
                />
              </div>
            </div>

            <div className="ap-field">
              <label>Opis</label>
              <textarea name="opis" value={form.opis} onChange={onChange} rows={4} />
            </div>

            <div className="ap-actions">
              <button className="ap-btn ap-btnPrimary" type="submit">
                {form.id ? "Sačuvaj izmene" : "Kreiraj izložbu"}
              </button>

              {form.id && (
                <button className="ap-btn" type="button" onClick={resetForm}>
                  Otkaži izmenu
                </button>
              )}

              <button className="ap-btn ap-btnGhost" type="button" onClick={ucitaj}>
                Osveži listu
              </button>
            </div>

            {poruka && <div className="ap-alert ap-ok">{poruka}</div>}
            {greska && <div className="ap-alert ap-err">{greska}</div>}
          </form>
        </div>

        <div className="ap-card" style={{ marginTop: 16 }}>
          <h3 className="ap-cardTitle">Sve izložbe</h3>

          {loading ? (
            <div className="ap-state">Učitavanje...</div>
          ) : greska ? (
            <div className="ap-alert ap-err">{greska}</div>
          ) : izlozbe.length === 0 ? (
            <div className="ap-state">Nema izložbi.</div>
          ) : (
            <div className="ap-table">
              {izlozbe.map((i) => (
                <div className="ap-row" key={i.id}>
                  <div className="ap-rowMain">
                    <div className="ap-rowTitle">{i.naziv}</div>
                    <div className="ap-rowMeta">
                      {i.lokacija} • {i.datum} • mesta: {i.dostupna_mesta}
                    </div>
                  </div>

                  <div className="ap-rowActions">
                    <button className="ap-btn" onClick={() => edit(i)} type="button">
                      Izmeni
                    </button>
                    <button className="ap-btn ap-btnDanger" onClick={() => obrisi(i.id)} type="button">
                      Obriši
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        
      </div>
    </div>
  );
}