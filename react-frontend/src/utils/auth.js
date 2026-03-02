export const getToken = () => localStorage.getItem("token");
export const getKorisnikId = () => localStorage.getItem("korisnikId");
export const getUloga = () => localStorage.getItem("uloga");

export const isLoggedIn = () => !!getToken();

export const isAdmin = () => getUloga() === "admin";
export const isFotograf = () => getUloga() === "fotograf";
export const isPosetilac = () => getUloga() === "posetilac";