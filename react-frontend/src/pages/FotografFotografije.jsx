import { useEffect, useState } from "react";
import axios from "axios";
import "./FotografFotografije.css";

const API = "http://127.0.0.1:8000/api";

export default function FotografFotografije() {
  const token = localStorage.getItem("token");

  const [izlozbe, setIzlozbe] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");
  const [poruka, setPoruka] = useState("");

  const [form, setForm] = useState({
    naziv: "",
    opis: "",
    izlozba_id: "",
    slika: null,
  });

  const ucitajIzlozbe = async () => {
    setLoading(true);
    setGreska("");
    setPoruka("");

    try {
      const res = await axios.get(`${API}/izlozbe`, {
        headers: { Accept: "application/json" },
      });
      const data = res.data?.data ?? res.data;
      setIzlozbe(Array.isArray(data) ? data : []);
    } catch (e) {
      setGreska(
        e?.response?.data?.poruka ||
          e?.response?.data?.message ||
          "Greška pri učitavanju izložbi."
      );
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    ucitajIzlozbe();
    
  }, []);

  const onChange = (e) => {
    const { name, value } = e.target;
    setForm((p) => ({ ...p, [name]: value }));
  };

  const onFile = (e) => {
    const file = e.target.files?.[0] || null;
    setForm((p) => ({ ...p, slika: file }));
  };

  const submit = async (e) => {
    e.preventDefault();
    setPoruka("");
    setGreska("");

    if (!token) {
      setGreska("Nisi prijavljen (nema tokena).");
      return;
    }
    if (!form.izlozba_id) {
      setGreska("Izaberi izložbu.");
      return;
    }
    if (!form.slika) {
      setGreska("Izaberi sliku za upload.");
      return;
    }

    try {
      const fd = new FormData();
      fd.append("naziv", form.naziv);
      fd.append("opis", form.opis);
      fd.append("izlozba_id", form.izlozba_id);
      fd.append("slika", form.slika);

      await axios.post(`${API}/fotografije`, fd, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
          "Content-Type": "multipart/form-data",
        },
      });

      setPoruka("Fotografija je uspešno dodata i povezana sa izložbom.");
      setForm({ naziv: "", opis: "", izlozba_id: "", slika: null });

      const fileInput = document.getElementById("foto-file");
      if (fileInput) fileInput.value = "";
    } catch (e2) {
      setGreska(
        e2?.response?.data?.poruka ||
          e2?.response?.data?.message ||
          "Greška pri dodavanju fotografije."
      );
    }
  };

  return (
    <div className="fp-page">
      <div className="fp-container">
        <div className="fp-head">
          <h2>Fotograf</h2>
        </div>

        <div className="fp-card">
          <h3>Dodaj fotografiju</h3>

          {loading ? (
            <div className="fp-state">Učitavanje izložbi...</div>
          ) : (
            <form className="fp-form" onSubmit={submit}>
              <div className="fp-grid2">
                <div className="fp-field">
                  <label>Naziv</label>
                  <input
                    name="naziv"
                    value={form.naziv}
                    onChange={onChange}
                    required
                    placeholder="npr. Jutarnje svetlo"
                  />
                </div>

                <div className="fp-field">
                  <label>Izložba</label>
                  <select
                    name="izlozba_id"
                    value={form.izlozba_id}
                    onChange={onChange}
                    required
                  >
                    <option value="">— Izaberi izložbu —</option>
                    {izlozbe.map((i) => (
                      <option key={i.id} value={i.id}>
                        {i.naziv}
                      </option>
                    ))}
                  </select>
                </div>
              </div>

              <div className="fp-field">
                <label>Opis</label>
                <textarea
                  name="opis"
                  value={form.opis}
                  onChange={onChange}
                  rows={3}
                  placeholder="Kratak opis fotografije"
                />
              </div>

              <div className="fp-field">
                <label>Slika</label>
                <input
                  id="foto-file"
                  type="file"
                  accept="image/*"
                  onChange={onFile}
                  required
                />
              </div>

              <div className="fp-actions">
                <button className="fp-btn fp-primary" type="submit">
                  Uploaduj
                </button>
                <button className="fp-btn fp-ghost" type="button" onClick={ucitajIzlozbe}>
                  Osveži izložbe
                </button>
              </div>

              {poruka && <div className="fp-alert fp-ok">{poruka}</div>}
              {greska && <div className="fp-alert fp-err">{greska}</div>}
            </form>
          )}
        </div>
      </div>
    </div>
  );
}