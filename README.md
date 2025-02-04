# camagru

A web application for photo editing and social sharing.

## Project Overview
Camagru is a web application that allows users to create and share photo edits by combining webcam captures or uploaded images with predefined overlay images.

## Core Features

### User Management
- User registration with email verification
- Secure login/logout functionality
- Password reset capability
- Profile management (username, email, password updates)

### Photo Editing
- Webcam integration for photo capture
- Image upload alternative
- Predefined overlay images with alpha channel
- Server-side image processing
- Live preview capability

### Gallery
- Public gallery of all edited images
- Pagination (5+ images per page)
- Like and comment functionality for authenticated users
- Email notifications for new comments (toggleable)

## Technical Requirements

### Backend
- Any server-side language (must have PHP standard library equivalent)
- No external frameworks beyond PHP standard library equivalents
- Secure against:
  - SQL injection
  - XSS attacks
  - CSRF
  - Unauthorized uploads
  - Password exposure

### Frontend
- HTML5, CSS, native JavaScript
- CSS frameworks allowed (no JS dependencies)
- Mobile-responsive design
- Compatible with Firefox (≥41) and Chrome (≥46)

### Security
- Encrypted password storage
- Form validation
- Protected routes
- Environment variable management (.env)
- CORS handling

### Deployment
- Containerized setup (Docker or equivalent)
- Single command deployment

## Additional Notes
- MVC architecture recommended
- Clean console (no errors/warnings)
- Secure credential storage (use .env, never commit)