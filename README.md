# Space-Xplorer

A game of universe exploration.

## Description

Space Xplorer is a web-based game where players can explore and discover different star systems, planets, and celestial objects in a virtual universe.

**MVP (Current Version)** : Each player starts with a randomly generated home planet and can explore its characteristics. Exploration features will be added progressively.

## Features

### Current Features (MVP)

- **User Registration/Login** : Account creation and authentication
- **Home Planet Generation** : Automatic random generation of a home planet upon registration
- **Planet Visualization** : Display of home planet characteristics
- **User Profile** : User profile management

### Planned Features

- Exploration of other planets
- Discovery of star systems
- Progression and achievements system
- Player interactions
- And more...

## Technology Stack

### Backend
- **Laravel 12** - PHP framework with event-driven architecture
- **Laravel Sanctum** - API token authentication
- **Laravel Telescope** - Debugging and monitoring
- **Laravel Horizon** - Queue monitoring
- **Laravel Pint** - Code formatting

### Frontend
- **Livewire 3** - Interactive server-side components
- **Tailwind CSS** - Utility-first CSS framework (with custom design system)
- **Alpine.js** - Lightweight JavaScript framework
- **Vite** - Build tool for assets

### Database & Infrastructure
- **MySQL 8.0** - Relational database
- **Redis** - Cache and queue backend
- **Laravel Sail** - Docker development environment
- **Laravel Forge** - Deployment platform

## Documentation

For detailed project documentation, see [AGENTS.md](./AGENTS.md).

The documentation includes:
- **[PROJECT_BRIEF.md](./docs/memory_bank/PROJECT_BRIEF.md)** - Business vision, features, personas, user flows
- **[ARCHITECTURE.md](./docs/memory_bank/ARCHITECTURE.md)** - Technical architecture, data model, API endpoints
- **[STACK.md](./docs/memory_bank/STACK.md)** - Complete technology stack details

## Getting Started

### Prerequisites

- Docker & Docker Compose
- Git

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/space-xplorer.git
   cd space-xplorer
   ```

2. **Install dependencies using Laravel Sail**
   ```bash
   ./vendor/bin/sail up -d
   ```

3. **Install PHP dependencies**
   ```bash
   ./vendor/bin/sail composer install
   ```

4. **Install Node dependencies**
   ```bash
   ./vendor/bin/sail npm install
   ```

5. **Set up environment file**
   ```bash
   cp .env.example .env
   ./vendor/bin/sail artisan key:generate
   ```

6. **Run migrations**
   ```bash
   ./vendor/bin/sail artisan migrate
   ```

7. **Build assets**
   ```bash
   ./vendor/bin/sail npm run dev
   ```

8. **Access the application**
   - Web: http://localhost
   - Telescope: http://localhost/telescope (if enabled)

### Development

- Run tests: `./vendor/bin/sail artisan test`
- Format code: `./vendor/bin/sail pint`
- Access container: `./vendor/bin/sail shell`

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

- Follow Laravel coding standards
- Use Laravel Pint for code formatting
- Write tests for new features
- Update documentation as needed

## License

[License information to be added]

