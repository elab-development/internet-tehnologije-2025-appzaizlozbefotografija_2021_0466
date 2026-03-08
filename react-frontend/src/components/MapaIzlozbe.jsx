import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';

export default function MapaIzlozbe({ lat, lon, naziv }) {
  if (!lat || !lon) {
    return <p>Lokacija nije dostupna.</p>;
  }

  return (
    <div style={{ marginTop: '20px' }}>
      <h3>Lokacija izložbe</h3>

      <MapContainer
        center={[lat, lon]}
        zoom={13}
        style={{ height: '300px', width: '100%', borderRadius: '12px' }}
      >
        <TileLayer
          url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        />
        <Marker position={[lat, lon]}>
          <Popup>{naziv}</Popup>
        </Marker>
      </MapContainer>
    </div>
  );
}