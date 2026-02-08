import { useEffect, useMemo, useState } from "react";
import { useParams, Link } from "react-router-dom";
import axios from "axios";
import "./Galerija.css";

const API = "http://127.0.0.1:8000/api";

export default function Galerija() {
  const { id } = useParams();

  const [fotografije, setFotografije] = useState([]);
  const [loading, setLoading] = useState(true);
  const [greska, setGreska] = useState("");
  const [openSrc, setOpenSrc] = useState(null);

  const token = localStorage.getItem("token") || "";

  
  const isAdmin = useMemo(() => {
    const uloga = (localStorage.getItem("uloga") || "").toLowerCase();
    if (uloga === "admin") return true;

    try {
      const korisnik = JSON.parse(localStorage.getItem("korisnik") || "null");
      return (korisnik?.uloga || "").toLowerCase() === "admin";
    } catch {
      return false;
    }
  }, []);

  const headers = useMemo(
    () => ({
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
    }),
    [token]
  );

  
  const extractArray = (payload) => {
    if (Array.isArray(payload)) return payload;
    if (Array.isArray(payload?.data)) return payload.data;
    if (Array.isArray(payload?.fotografije)) return payload.fotografije;
    if (Array.isArray(payload?.data?.data)) return payload.data.data;
    return [];
  };

  const ucitaj = () => {
    setLoading(true);
    setGreska("");

    axios
      .get(`${API}/izlozbe/${id}/fotografije`, { headers: { Accept: "application/json" } })
      .then((res) => {
        const arr = extractArray(res.data);
        setFotografije(arr);
      })
      .catch(() => setGreska("Greška pri učitavanju galerije"))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    ucitaj();
    
  }, [id]);

  const getImgSrc = (f) => {
    const p = (f.putanja_slike || "").trim();
    if (!p) return "";
    if (p.startsWith("http")) return p;
    const clean = p.startsWith("/") ? p.slice(1) : p;
    return `http://127.0.0.1:8000/${clean}`;
  };

  const uvecaj = (src) => {
    if (src) setOpenSrc(src);
  };

  const obrisi = async (fotoId) => {
    setGreska("");

    if (!isAdmin) {
      setGreska("Nemate dozvolu za brisanje.");
      return;
    }

    if (!token) {
      setGreska("Niste prijavljeni (nema tokena).");
      return;
    }

    if (!window.confirm("Da li sigurno želiš da obrišeš ovu fotografiju?")) return;

    try {
      await axios.delete(`${API}/fotografije/${fotoId}`, { headers });
      setFotografije((prev) => prev.filter((x) => x.id !== fotoId));
    } catch (e) {
      setGreska(
        e?.response?.data?.poruka ||
          e?.response?.data?.message ||
          "Greška pri brisanju fotografije."
      );
    }
  };

  if (loading) return <div className="gal-state">Učitavanje...</div>;
  if (greska) return <div className="gal-state gal-state--error">{greska}</div>;

  return (
    <div className="gal-page">
      <div className="gal-container">
        <div className="gal-header">
          <div>
            <h2 className="gal-title">Galerija</h2>
            
          </div>

          <Link className="gal-back" to={`/izlozbe/${id}`}>
            ← Nazad na izložbu
          </Link>
        </div>

        {fotografije.length === 0 ? (
          <div className="gal-state">Nema fotografija za ovu izložbu.</div>
        ) : (
          <div className="gal-grid">
            {fotografije.map((f) => {
              const src = getImgSrc(f);

              return (
                <div
                  key={f.id}
                  className="gal-card"
                  role="button"
                  tabIndex={0}
                  onClick={() => uvecaj(src)}
                  onKeyDown={(e) => {
                    if (e.key === "Enter" || e.key === " ") uvecaj(src);
                  }}
                >
                  <div className="gal-imgWrap">
                    <img
                      className="gal-img"
                      src={src}
                      alt={f.naziv || "Fotografija"}
                      loading="lazy"
                    />

                    <div className="gal-overlay">
                      <span>Uvećaj</span>
                    </div>

                    {}
                    {isAdmin && (
                      <button
                        className="gal-deleteBtn"
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation(); 
                          obrisi(f.id);
                        }}
                        aria-label="Obriši fotografiju"
                        title="Obriši"
                      >
                        Obriši
                      </button>
                    )}
                  </div>

                  <div className="gal-info">
                    <div className="gal-name">{f.naziv}</div>
                    {f.opis && <div className="gal-desc">{f.opis}</div>}
                  </div>
                </div>
              );
            })}
          </div>
        )}
      </div>

      {openSrc && (
        <div className="gal-modal" onClick={() => setOpenSrc(null)} role="dialog">
          <div className="gal-modalInner" onClick={(e) => e.stopPropagation()}>
            <button
              className="gal-close"
              onClick={() => setOpenSrc(null)}
              type="button"
              aria-label="Zatvori"
            >
              ✕
            </button>
            <img className="gal-modalImg" src={openSrc} alt="Uvećana fotografija" />
          </div>
        </div>
      )}
    </div>
  );
}