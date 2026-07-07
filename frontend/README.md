# Order Panel — Frontend

Angular SPA for the Order Panel application. See the [root README](../README.md) for the full project overview and Docker setup.

## Development server

In the Docker setup the frontend runs automatically at `http://localhost:4200`. To run it standalone:

```bash
npm install
npm start
```

## Tests

Unit tests run with [Jest](https://jestjs.io):

```bash
npm test            # single run
npm run test:watch  # watch mode
npm run test:coverage
```

## Building

```bash
npm run build
```

Build artifacts are stored in the `dist/` directory. The Docker image build (see [docker/frontend/Dockerfile](../docker/frontend/Dockerfile)) selects the Angular configuration via the `CONFIG` build argument (`staging` or `production`).
