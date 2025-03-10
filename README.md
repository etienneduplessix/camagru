# Camagru

## Project Overview
Camagru is a web application that allows users to create and share photo edits using their webcam. The application enables users to superimpose predefined images with alpha channels onto photos captured from their webcam or uploaded from their device. All created images are public, and can be liked and commented on by registered users.

## Technical Requirements

### Technologies
- **Server-side**: Any language (equivalent to PHP standard library)
- **Client-side**: HTML, CSS, JavaScript (browser native APIs only)
- **Frameworks**: 
  - Server-side: Any (up to PHP standard library equivalent)
  - Client-side: CSS frameworks allowed (without JavaScript)
- **Containerization**: Must use container technology (Docker or equivalent)
- **Browser Compatibility**: Firefox (≥41) and Chrome (≥46)

### Security Requirements
# Camagru Security Testing Checklist

## Password Security
- [ ] Passwords are hashed using a strong algorithm (bcrypt, Argon2, or PBKDF2)
- [ ] Password requirements enforce minimum complexity (length, special characters, numbers)
- [ ] Password reset functionality does not reveal if an email exists in the database
- [ ] Temporary password reset tokens have short expiration times
- [ ] Failed login attempts are limited or have increasing delays

## Protection Against Injection Attacks
### SQL Injection
- [ ] All database queries use prepared statements or parameterized queries
- [ ] Input validation is performed on all form fields
- [ ] Error messages don't reveal database information
- [ ] Database user has minimal required privileges

### XSS (Cross-Site Scripting)
- [ ] All user input is sanitized before being displayed
- [ ] HTML special characters are escaped
- [ ] Content Security Policy (CSP) headers are implemented
- [ ] User-submitted content (comments, usernames) is properly escaped when rendered
- [ ] Output encoding is used based on the context (HTML, JavaScript, CSS, URL)

## File Upload Security
- [ ] File types are strictly validated (whitelist approach)
- [ ] File extensions are checked against actual file content (MIME type validation)
- [ ] Uploaded files are renamed to random names
- [ ] File size limits are enforced
- [ ] Uploaded files are stored outside the web root when possible
- [ ] Images are processed/resized server-side to remove any embedded malicious code

## CSRF Protection
- [ ] Anti-CSRF tokens are implemented in all forms
- [ ] Tokens are validated on form submission
- [ ] Tokens are unique per session and form
- [ ] Same-Site cookie attribute is set

## Session Security
- [ ] Session IDs are regenerated after login
- [ ] Sessions have reasonable timeout periods
- [ ] Session cookies have the Secure and HttpOnly flags
- [ ] Session data is stored securely on the server
- [ ] Sessions are terminated on logout

## General Web Security
- [ ] Security headers are implemented (X-XSS-Protection, X-Content-Type-Options, etc.)
- [ ] HTTPS is used for all communications
- [ ] Authentication is required for all protected routes
- [ ] Authorization checks prevent users from accessing other users' data
- [ ] Input validation is performed server-side (not just client-side)

## Environment Security
- [ ] All credentials, API keys, and sensitive configuration are in .env file
- [ ] .env file is included in .gitignore
- [ ] Production credentials differ from development
- [ ] Error handling doesn't expose sensitive information in production
- [ ] No hardcoded credentials in the codebase

## API Security
- [ ] API endpoints validate authentication
- [ ] Rate limiting is implemented on authentication endpoints
- [ ] API responses don't include sensitive data
- [ ] Authentication tokens are properly validated

## Database Security
- [ ] Database is not directly accessible from the internet
- [ ] Default database credentials are changed
- [ ] Database backups are secured
- [ ] Sensitive data is encrypted in the database when necessary

## Testing Tools & Methods
- [ ] Automated vulnerability scanning
- [ ] Penetration testing
- [ ] Code review focused on security
- [ ] Test for common OWASP Top 10 vulnerabilities

## Core Features

### Common Features
- MVC structure recommended
- Responsive design (mobile-friendly)
- Form validations
- Secure implementation

### User Features
- User registration with email, username, and secure password
- Email confirmation for account activation
- User authentication (login)
- Password reset functionality
- One-click logout from any page
- Profile management (modify username, email, password)

### Gallery Features
- Public gallery displaying all user-created images
- Images ordered by creation date
- Like and comment functionality (for authenticated users)
- Email notifications for new comments (with option to disable)
- Pagination (minimum 5 items per page)

### Editing Features
- Restricted to authenticated users
- Main section with:
  - Webcam preview
  - Selectable superimposable images
  - Capture button (inactive until an image is selected)
- Side section displaying thumbnails of user's previous images
- Server-side image processing for superimposing images
- Option to upload images instead of using webcam
- Ability for users to delete their own images

## Bonus Features (only evaluated if mandatory part is perfect)
- AJAX for server communication
- Live preview of edited result
- Infinite pagination in gallery
- Social media sharing
- Animated GIF rendering

## Getting Started
1. Clone this repository
2. Set up your environment variables in a `.env` file
3. Build and start the containers with docker-compose
4. Access the application through your browser

## Notes
- All captured images are public and can be viewed by anyone
- Superimposable images must have alpha channels for proper effect
- No errors or warnings should appear in console (client or server-side)