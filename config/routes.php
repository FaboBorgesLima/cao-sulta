<?php

use App\Controller\AuthenticationController;
use App\Controller\CRMVRegisterController;
use App\Controller\PermissionController;
use App\Controller\PetController;
use App\Controller\StaticPagesController;
use App\Controller\UserController;
use App\Controller\VetController;
use Core\Router\Route;

Route::get("/", [StaticPagesController::class, "home"])->name("home");
Route::get("/register", [StaticPagesController::class, "register"])->name("register");

// ---------- User
Route::get("/user/create", [UserController::class, "create"])->name("user.create");
Route::get("/user/{id}", [UserController::class, "show"])->name("user.show");
Route::post("/user/store", [UserController::class, "store"])->name("user.store");
Route::get("/user", [UserController::class, "index"])->name("user.index");

// ---------- Auth
Route::get("/auth", [AuthenticationController::class, "login"])->name("auth.login");
Route::get("/auth/user/{id}/{cpf}", [AuthenticationController::class, "send"])->name("auth.send");
Route::get("/auth/{token}", [AuthenticationController::class, "authenticate"])->name("auth.auth");
Route::post("/auth/user", [AuthenticationController::class, "find"])->name("auth.find");

// ---------- Vet

Route::get("/vet/create", [VetController::class, "create"])->name("vet.create");
Route::post("/vet/store", [VetController::class, "store"])->name("vet.store");

// ---------- CRMV Register

Route::get("/user/{profile}/crmv-register", [CRMVRegisterController::class, "index"])->name("crmv-register.index");

// ---------- Pet
Route::get("/user/{id}/pets", [PetController::class, "index"])->name("pet.index");

Route::middleware("auth")->group(function () {
    Route::get("/dashboard", [UserController::class, "dashboard"])->name("dashboard");

    // ---------- Pet
    Route::get("/pet/create", [PetController::class, "create"])->name("pet.create");
    Route::post("/pet/store", [PetController::class, "store"])->name("pet.store");
    Route::get("/pet/{id}/update", [PetController::class, "update"])->name("pet.update");
    Route::post("/pet/{id}/save", [PetController::class, "save"])->name("pet.save");
    Route::get("/pet/{id}/delete", [PetController::class, "delete"])->name("pet.delete");


    // ---------- Auth
    Route::get("/logout", [AuthenticationController::class, "logout"])->name("auth.logout");

    // ---------- Permission

    Route::get("/permission", [PermissionController::class, 'index'])->name("permission.index");
    Route::post("/permission/create", [PermissionController::class, 'store'])->name("permission.store");
    Route::delete("/vet/{vet}/user/{user}/permission", [PermissionController::class, 'destroy'])->name("permission.destroy");
    Route::put("/vet/{vet}/user/{user}/permission", [PermissionController::class, 'update'])->name("permission.update");
});

Route::middleware("vet")->group(function () {

    // ---------- CRMV Register
    Route::delete("/crmv-register/{crmv_register}", [CRMVRegisterController::class, "destroy"])
        ->name("crmv-register.destroy");
    Route::get("/crmv-register/{crmv_register}/update", [CRMVRegisterController::class, "update"])
        ->name("crmv-register.update");
    Route::post("/crmv-register/{crmv_register}/update", [CRMVRegisterController::class, "save"])
        ->name("crmv-register.save");
    Route::get("/crmv-register/create", [CRMVRegisterController::class, "create"])->name("crmv-register.create");
    Route::post("/crmv-register/create", [CRMVRegisterController::class, "store"])->name("crmv-register.store");
});
