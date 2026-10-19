# Entity-Relationship Diagram (ERD) - Bookstore Database

## Entities and Attributes

| Table | Attributes (Keys & Constraints) |
| :--- | :--- |
| **publishers** | `id` (PK), `name`, `address`, `website` |
| **authors** | `id` (PK), `name`, `biography`, `nationality` |
| **categories** | `id` (PK), `name` (UNIQUE), `description`, `parent_category_id` (FK) |
| **books** | `id` (PK), `title`, `isbn` (UNIQUE), `price`, `stock`, `publication_date`, `publisher_id` (FK) |
| **book_authors** | `book_id` (PK, FK), `author_id` (PK, FK) |
| **book_categories**| `book_id` (PK, FK), `category_id` (PK, FK) |
| **customers** | `id` (PK), `name`, `email` (UNIQUE), `password_hash`, `address` |
| **orders** | `id` (PK), `customer_id` (FK), `order_date`, `status`, `total`, `shipping_address` |
| **order_items** | `order_id` (PK, FK), `book_id` (PK, FK), `quantity`, `unit_price`, `subtotal` |
| **reviews** | `id` (PK), `book_id` (FK), `customer_id` (FK), `rating`, `comment`, `review_date` |

## Relationships & Cardinality

1. **Publishers to Books (1:N):**
   * One `publisher` publishes many `books`.
   * One `book` belongs to exactly one `publisher`.
   * *Notation:* `publishers` ||---|{ `books`

2. **Books to Authors (M:N) -> Resolved via `book_authors`:**
   * One `book` has many `authors`. One `author` writes many `books`.
   * *Notation:* `books` ||---|{ `book_authors` }|---|| `authors`

3. **Books to Categories (M:N) -> Resolved via `book_categories`:**
   * One `book` belongs to many `categories`. One `category` has many `books`.
   * *Notation:* `books` ||---|{ `book_categories` }|---|| `categories`

4. **Customers to Orders (1:N):**
   * One `customer` places many `orders`.
   * One `order` belongs to exactly one `customer`.
   * *Notation:* `customers` ||---|{ `orders`

5. **Orders to Books (M:N) -> Resolved via `order_items`:**
   * One `order` contains many `order_items`. One `book` can be in many `order_items`.
   * *Notation:* `orders` ||---|{ `order_items` }|---|| `books`

6. **Customers & Books to Reviews (1:N):**
   * One `customer` writes many `reviews`. One `book` receives many `reviews`.
   * *Notation:* `customers` ||---|{ `reviews` }|---|| `books`