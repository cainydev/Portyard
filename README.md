<div align="center">
    <img src="resources/images/portyard_v2.png" alt="portyard" width="120">
</div>

# Portyard Registry

A container image registry and management UI built on Laravel and Filament.

## Cloud Service: [portyard.de](https://portyard.de)

To push an image to the official hosted service:

1. Create an account at [https://portyard.de](https://portyard.de) and create and repository.

2. Tag the image:
   `docker tag <local-image> portyard.de/<username>/<repository>:<tag>`

   Example:
   `docker tag my-app:latest portyard.de/myuser/my-app:1.0.0`

3. Authenticate:
   `docker login portyard.de`

4. Push:
   `docker push portyard.de/<username>/<repository>:<tag>`

Replace `<username>`, `<repository>`, and `<tag>` with your values.

## Self-hosting (minimal)

You can self-host Portyard. Most things will work with a Docker Compose deployment.
1. Clone the repo
2. Set APP_URL and database values in .env
3. Generate JWT keys for Dockhand and point DOCKHAND_PRIVATE_KEY/DOCKHAND_PUBLIC_KEY to them
4. Run Docker Compose

## License

See the LICENSE file in this repository.

## Contact

Report issues via GitHub Issues or reach out on GitHub: [@cainydev](https://github.com/cainydev)
