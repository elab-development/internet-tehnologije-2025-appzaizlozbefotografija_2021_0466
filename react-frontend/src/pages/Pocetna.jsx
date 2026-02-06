import { Link } from "react-router-dom";
import Button from "../components/Button";
import "./Pocetna.css";

export default function Pocetna() {
  return (
    <div className="pocetna">
      <section className="pocetna-hero">
        <div className="pocetna-hero__inner">
          <h1>Aplikacija za izložbe fotografija</h1>
          <p>
            Dobrodošli na platformu za pregled i prijavu na fotografske izložbe.
          </p>

          <Link to="/izlozbe">
            <Button>Pogledaj izložbe</Button>
          </Link>
        </div>
      </section>

      <section className="pocetna-galerija">
        <div className="pocetna-galerija__inner">
          <div className="pocetna-slika slika-1" />
          <div className="pocetna-slika slika-2" />
          <div className="pocetna-slika slika-3" />
        </div>
      </section>
    </div>
  );
}