import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';

@Component({
    selector: 'app-root',
    imports: [RouterOutlet],
    template: `
      <main class="app-container container-fluid">
        <router-outlet></router-outlet>
      </main>
    `,
    styles: [`
        main {
          min-height: 100vh;
          background: linear-gradient(135deg, #f8fafc, #e9f0f7);
        }
      `]
  })
  export class AppComponent {}
