import { Component, AfterViewInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormControl, Validators, FormBuilder } from '@angular/forms';

import { ApiService } from '../../services/api.service';
import { User } from '../../models/user';
import { TypeDocument } from '../../models/type_document';

declare var M: any; 

@Component({
  selector: 'app-user',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    CommonModule
  ],
  templateUrl: './user.component.html',
  styleUrl: './user.component.css'
})
export class UserComponent  implements AfterViewInit {

  constructor(private fb:FormBuilder, public api:ApiService) {}

  types_document:TypeDocument[] = [
    {
      id_type_document: 1,
      name: "C.C",
    },
    {
      id_type_document: 2,
      name: "T.I",
    }
  ];
  search_result:User[] = [];
  habilitar:boolean = true;
  active_search:boolean = false;
  id_user:number = 0;

  form_user = this.fb.group({
    email: ["", [Validators.required, Validators.email,]],
    password: ["", Validators.required,],
    confirm_password: ["", Validators.required,],
    first_name: ["", Validators.required],
    last_name: ["", Validators.required],
    type_document: ["", Validators.required],
    document: ["", [Validators.required, Validators.pattern(/^[0-9]*$/)]],
    phone: ["", Validators.required],
    address: [""],
    birthdate: [""],
    is_admin: [false],
  });
  search_value:FormControl = new  FormControl("", Validators.required);

  ngAfterViewInit() {
    // Inicializar los componentes de Materialize
    M.AutoInit();

    this.loadTypesDocuments();
    
  }

  getFormControl(key:string) {
    return this.form_user.get(key) as FormControl;

  }//Fin this.getFormControl(){...}

  mostrarMensaje(mensaje:string) {
    M.toast({html: mensaje});
  }

  enabledInterface() {

    this.habilitar = true;
    this.getFormControl("email").enable();
    this.getFormControl("password").enable();
    this.getFormControl("confirm_password").enable();
    this.getFormControl("first_name").enable();
    this.getFormControl("last_name").enable();
    this.getFormControl("type_document").enable();
    this.getFormControl("document").enable();
    this.getFormControl("phone").enable();
    this.getFormControl("address").enable();
    this.getFormControl("birthdate").enable();
    
    if (this.api.user?.is_admin) {
      this.getFormControl("is_admin").enable();
    }

  }//Fin enabledInterface() {...}

  disabledInterface() {

    this.habilitar = true;
    this.getFormControl("email").disable();
    this.getFormControl("password").disable();
    this.getFormControl("confirm_password").disable();
    this.getFormControl("first_name").disable();
    this.getFormControl("last_name").disable();
    this.getFormControl("type_document").disable();
    this.getFormControl("document").disable();
    this.getFormControl("phone").disable();
    this.getFormControl("address").disable();
    this.getFormControl("birthdate").disable();
    this.getFormControl("is_admin").disable();

  }//Fin disabledInterface() {...}

  loadTypesDocuments() {

    this.api.listTypesDocuments().subscribe(
      (response) => {
        if (response.types_document) {
          //this.types_document = response.types_document;
          this.initInterface();
        }
      }
    );

  }//Fin loadTypesDocuments(){...}

  initInterface() {

    if (!this.api.user?.is_admin) {
      this.getFormControl("is_admin").disable();

      this.btnEditClick(this.api.user!);
    }

  }

  btnNewUserClick() {

    //Limpiamos los campos
    this.id_user = 0;
    this.search_result = [];
    this.active_search = false;

    this.getFormControl("email").setValue("");
    this.getFormControl("password").setValue("");
    this.getFormControl("confirm_password").setValue("");
    this.getFormControl("first_name").setValue("");
    this.getFormControl("last_name").setValue("");
    this.getFormControl("type_document").setValue("");
    this.getFormControl("document").setValue("");
    this.getFormControl("phone").setValue("");
    this.getFormControl("address").setValue("");
    this.getFormControl("birthdate").setValue("");
    this.getFormControl("is_admin").setValue(false);
    this.search_value.setValue("");

    //Habilitamos la interfaz
    this.enabledInterface();

  }//Fin btnNewUserClick() {...}

  btnShowSearchClick() {
    this.active_search = true;

  }//Fin btnShowSearchClick(){...}

  btnSearchClick() {

    if (this.search_value.value.trim() == "") {
      this.mostrarMensaje("Debe ingresar el valor de busqueda");
      return;
    }

    this.search_result = [];
    this.disabledInterface();
    this.api.searchUser(this.search_value.value.trim()).subscribe(
      (response) => {
        if (response.users) {
          this.search_result = response.users;
        }
      }
    );

  }//Fin btnSearchClick(){...}

  btnEditClick(user:User) {

    this.btnNewUserClick();

    this.id_user = user.id_user;
    this.getFormControl("email").setValue(user.email);
    this.getFormControl("first_name").setValue(user.first_name);
    this.getFormControl("last_name").setValue(user.last_name);
    this.getFormControl("type_document").setValue(user.type_document);
    this.getFormControl("document").setValue(user.document);
    this.getFormControl("phone").setValue(user.phone);
    this.getFormControl("address").setValue(user.address);
    this.getFormControl("birthdate").setValue(user.birthdate);
    this.getFormControl("is_admin").setValue(user.is_admin);

  }//Fin btnEditClick() {...}

  btnSaveUserClick() {

    if (this.getFormControl("email").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar la dirección email");
      return;
    }

    if (this.getFormControl("email").invalid) {
      this.mostrarMensaje("la dirección email no tiene la estructura adecuada");
      return;
    }

    //La contraseña solo la validamos en caso de que sea una creación o en caso de que el campo haya sido modificado
    if (this.id_user == 0 || this.getFormControl("password").value?.trim() != "" && this.getFormControl("confirm_password").value?.trim() == "") {
      if (this.getFormControl("password").invalid) {
        this.mostrarMensaje("Debe ingresar la contraseña");
        return;
      }

      if (this.getFormControl("confirm_password").invalid) {
        this.mostrarMensaje("Debe confirmar la contraseña");
        return;
      }

      if (this.getFormControl("password").value?.trim() != this.getFormControl("confirm_password").value?.trim()) {
        this.mostrarMensaje("Las contraseñas ingresadas no coinciden");
        return;
      }
    }
    
    //Verificamos los campos requeridos
    if (this.getFormControl("first_name").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar el nombre");
      return;
    }

    if (this.getFormControl("last_name").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar el apellido");
      return;
    }

    if (this.getFormControl("type_document").value?.toString().trim() == "") {
      this.mostrarMensaje("Debe seleccionar el tipo de documento");
      return;
    }

    if (this.getFormControl("document").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar el número de documento");
      return;
    }

    if (this.getFormControl("document").invalid) {
      this.mostrarMensaje("El número de documento solo puede contener números");
      return;
    }

    if (this.getFormControl("phone").value?.trim() == "") {
      this.mostrarMensaje("Debe ingresar el número de teléfono");
      return;
    }
    
    let user:User = {
      id_user: this.id_user,
      email: this.getFormControl("email").value?.trim(),
      password: this.getFormControl("password").value?.trim(),
      first_name: this.getFormControl("first_name").value?.trim(),
      last_name: this.getFormControl("last_name").value?.trim(),
      type_document: this.getFormControl("type_document").value,
      document: this.getFormControl("document").value?.trim(),
      phone: this.getFormControl("phone").value?.trim(),
      address: this.getFormControl("address").value?.trim(),
      birthdate: this.getFormControl("birthdate").value?.trim(),
      is_admin: this.getFormControl("is_admin").value,
    };
    
    this.disabledInterface();
    this.api.saveUser(user).subscribe(
      (response) => {
        if (response.user) {
          this.id_user = response.user.id_user;
          this.enabledInterface();
          this.mostrarMensaje("Usuario guardado");
        }
      }
    );

  }//Fin btnSaveUserClick() {...}

  btnDeleteUserClick() {

    if (this.id_user == 0) {
      this.mostrarMensaje("Debe cargar el usuario que desea eliminar");
      return;
    }

    this.disabledInterface();
    this.api.deleteUser(this.id_user).subscribe(
      (response) => {
        if (response.user) {
          this.btnNewUserClick();
          this.mostrarMensaje("Usuario eliminado");
        } 
      }
    );
    
  }//Fin btnDeleteUserClick(){...}

}
