# Dev.to Content Management System (CMS)

---

## Project Context

Dev.to aims to implement a comprehensive content management system to empower developers to share articles, explore relevant content, and collaborate efficiently. The platform will include a smooth front-office user experience and a robust admin dashboard to simplify the management of users, categories, tags, and articles.

The primary goal is to create a collaborative platform where developers and technology enthusiasts can register, write, and share articles while enjoying optimized navigation to discover high-quality content.

---

## Technologies Required

- **Programming Language:** PHP 8 (Object-Oriented Programming).
- **Database:** PDO as the driver for database interactions.

---

## Key Features

### Back Office (Admin)

#### 1. **Category Management**
   - Create, edit, and delete categories.
   - Associate multiple articles with a category.
   - View category statistics through charts.

#### 2. **Tag Management**
   - Create, edit, and delete tags.
   - Associate tags with articles for precise search.
   - View tag statistics through charts.

#### 3. **User Management**
   - View and manage user profiles.
   - Assign permissions to users to become authors.
   - Suspend or delete users for rule violations.

#### 4. **Article Management**
   - Review, accept, or reject submitted articles.
   - Archive inappropriate articles.
   - View most-read articles.

#### 5. **Statistics and Dashboard**
   - Detailed view of users, articles, categories, and tags.
   - Display top 3 authors based on published or read articles.
   - Interactive charts for categories and tags.
   - View most popular articles.

#### 6. **Detailed Pages**
   - **Single Article Page:** Full details of an article.
   - **Single Profile Page:** User profile view.

### Front Office (User)

#### 1. **Registration and Login**
   - Create an account with basic information (name, email, password).
   - Secure login with role-based redirection (Admin to Dashboard, User to Homepage).

#### 2. **Navigation and Search**
   - Interactive search bar to find articles, categories, or tags.
   - Dynamic navigation between articles and categories.

#### 3. **Content Display**
   - Latest articles displayed on the homepage or a dedicated section.
   - Recently added or updated categories displayed for quick discovery.
   - Redirect to a single article page displaying its content, associated categories, tags, and author information.

#### 4. **Author Space**
   - Create, edit, and delete articles.
   - Associate a single category and multiple tags with an article.
   - Manage published articles from a personal dashboard.

---

## Performance Criteria

### 1. Task Planning with a Management Tool
   Use a task management tool like Jira to plan and track project tasks. Develop user stories to understand requirements.

### 2. Daily Commits on GitHub
   Ensure daily commits for tracking changes, improving traceability, and simplifying conflict resolution.

### 3. Responsive Design
   - Implement responsive web design using a CSS framework.
   - Ensure web pages adjust seamlessly across all screen types for universal usability.

### 4. Form Validation
   - **Frontend Validation:** Use HTML5 and native JavaScript to minimize errors before submission.
   - **Backend Validation:** Prevent XSS and CSRF attacks.

### 5. Project Structure
   - Clear separation of business logic and architecture.

### 6. Security
   - **SQL Injection:** Use prepared or parameterized queries to prevent SQL injection attacks. Validate and escape input data.
   - **XSS (Cross-Site Scripting):** Escape data before displaying it on HTML pages to prevent XSS attacks.

---

## Icons and Styling

**Icons**
- 🔗 **Links:** GitHub Repository, Project Presentation.
- 🔧 **Tools:** Jira, UML Tools, PHP.
- 🌐 **Platform:** Responsive Design.

**Styling**
- **Bold:** Key points.
- **Bulleted Lists:** Clear organization of features.
- **Sections with Headings:** Improved readability.

---

Thank you for exploring this README! Happy coding! 🌟

