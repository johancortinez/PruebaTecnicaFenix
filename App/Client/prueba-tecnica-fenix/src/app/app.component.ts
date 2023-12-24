import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterOutlet } from '@angular/router';

import { ApiService } from './services/api.service';
import { LoginComponent } from './pages/login/login.component';
import { UserComponent } from './pages/user/user.component';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    CommonModule, 
    RouterOutlet,
    LoginComponent,
    UserComponent,
  ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {

  title = 'prueba-tecnica-fenix';
  constructor(public api:ApiService) {}

}
