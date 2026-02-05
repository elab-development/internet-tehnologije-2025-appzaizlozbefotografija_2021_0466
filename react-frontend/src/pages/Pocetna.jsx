import { Link } from "react-router-dom";

export default function Pocetna() {
  return (
    <div style={{ textAlign: "center", marginTop: "40px" }}>
      <h1>Aplikacija za izložbe fotografija</h1>

      <p>
        Dobrodošli na platformu za pregled i prijavu na fotografske izložbe.
      </p>

      <div style={{ marginTop: "30px" }}>
        <Link to="/izlozbe">
          <button style={{ padding: "10px 20px", fontSize: "16px" }}>
            Pogledaj izložbe
          </button>
        </Link>
      </div>
    </div>
  );
}