import "./Input.css";

export default function Input({
  label,
  name,
  type = "text",
  value,
  onChange,
  placeholder,
  error,
}) {
  return (
    <div className="field">
      {label && <label className="field__label" htmlFor={name}>{label}</label>}
      <input
        id={name}
        name={name}
        type={type}
        className={`field__input ${error ? "field__input--error" : ""}`}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
      />
      {error && <div className="field__error">{error}</div>}
    </div>
  );
}