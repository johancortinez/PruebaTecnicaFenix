import { Component, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormControl, Validators, FormGroup, FormBuilder } from '@angular/forms';

import { ApiService } from '../../services/api.service';

declare var M: any; 

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    CommonModule
  ],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements AfterViewInit {

  constructor(private fb:FormBuilder, private api:ApiService) {}

  form_login = this.fb.group({
    email: ["", [Validators.required, Validators.email,]],
    password: ["", Validators.required,],
  });
  habilitar:Boolean = true;

  ngAfterViewInit() {
    // Inicializar los componentes de Materialize
    M.AutoInit();
    
  }

  getFormControl(key:string) {
    return this.form_login.get(key) as FormControl;

  }//Fin this.getFormControl(){...}

  mostrarMensaje(mensaje:string) {
    M.toast({html: mensaje});
  }

  enabledInterface() {

    this.habilitar = true;
    this.getFormControl("email").enable();
    this.getFormControl("password").enable();

  }//Fin enabledInterface() {...}

  disabledInterface() {

    this.habilitar = true;
    this.getFormControl("email").disable();
    this.getFormControl("password").disable();

  }//Fin disabledInterface() {...}

  btnLoginClick() {

    if (this.getFormControl("email").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar la dirección email");
      return;
    }

    if (this.getFormControl("email").invalid) {
      this.mostrarMensaje("la dirección email no tiene la estructura adecuada");
      return;
    }

    if (this.getFormControl("password").invalid) {
      this.mostrarMensaje("Debe ingresar la contraseña");
      return;
    }

    this.disabledInterface();
    this.api.login(
      this.getFormControl("email").value.trim(), 
      this.getFormControl("password").value.trim()
    ).subscribe(
      (response) => {

        this.enabledInterface();
        if (response.user) {
          this.api.user = response.user;
          this.api.token = response.session.token;
        }

        if (response.messages) {
          this.mostrarMensaje(response.messages[0].message);
        }

      }
    );

  }//Fin btnLoginClick() {...}

}
