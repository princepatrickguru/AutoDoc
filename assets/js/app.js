function getUserCoordinates() {
  const defaultLocation = { lat: 7.6498, lng: 5.2214 };

  if (!navigator.geolocation) {
    return Promise.resolve(defaultLocation);
  }

  return new Promise((resolve) => {
    navigator.geolocation.getCurrentPosition(
      ({ coords }) => {
        resolve({ lat: coords.latitude, lng: coords.longitude });
      },
      () => {
        resolve(defaultLocation);
      },
      { enableHighAccuracy: false, maximumAge: 300000, timeout: 5000 }
    );
  });
}