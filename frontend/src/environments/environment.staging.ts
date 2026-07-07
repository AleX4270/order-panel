export const environment = {
    production: true,
    apiUrl: 'https://api-order-panel-dev.aregulski.pl/api',
    echoConfig: {
        key: 'zdoskpczrnk9z2ivxvbo',
        wsHost: 'ws-order-panel-dev.aregulski.pl',
        wsPort: 80,
        wssPort: 443,
        forceTls: true,
        authEndpoint: 'https://api-order-panel-dev.aregulski.pl/broadcasting/auth'
    },
    map: {
        tileProviderUrl: "https://tiles.openfreemap.org/styles/liberty",
        geocodingApiUrl: "https://nominatim.openstreetmap.org/search"
    }
};