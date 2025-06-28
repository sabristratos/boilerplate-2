# Gemini Project Guidelines

This document outlines the key technologies, commands, and architectural conventions for this project. I will use this as a reference to assist you effectively.

## Project Overview

This is a **Laravel** project built on the **TALL stack**:
-   **T**ailwind CSS
-   **A**lpine.js
-   **L**aravel
-   **L**ivewire

It uses **Vite** for frontend asset bundling. The project is configured with a `flux` component library, and its usage is documented in `.junie/guidelines.md`.

## Key Commands

Here are the primary commands for working with this project:

| Command | Description |
| :--- | :--- |
| `composer run dev` | Starts the development environment, running the PHP server, queue listener, and Vite dev server concurrently. |
| `npm run build` | Compiles and bundles frontend assets for production. |
| `vendor/bin/pint` | Runs Laravel Pint to format PHP code and ensure style consistency. |
| `composer run test` | Executes the Pest test suite to verify application functionality. |

## Architectural Notes

-   **Component-Based UI**: The frontend is heavily based on the **Flux UI** component library. When creating or modifying UI, I will adhere to the conventions and components detailed in `.junie/guidelines.md`.
-   **Livewire**: Application logic is primarily handled by Livewire components. I will create and modify Livewire components for new features.
-   **Spatie Packages**: The project utilizes several Spatie packages for key functionality:
    -   `spatie/laravel-medialibrary` for media management.
    -   `spatie/laravel-permission` for roles and permissions.
    -   `spatie/laravel-translatable` for multilingual content.
-   **CI/CD**: GitHub Actions are used for continuous integration:
    -   `lint.yml`: Ensures code style using `pint`.
    -   `tests.yml`: Runs the `pest` test suite.

I will follow these guidelines to ensure my contributions are consistent with your project's standards.
