# Order Tracking System

A lightweight web-based order tracking tool developed for **Maningi Meat Distributors** to reduce human error when packing customer orders for delivery.

## Overview

The Order Tracking System was developed to address a real operational problem encountered while working as an administrative assistant. Workers responsible for packing orders relied on manual counting, which sometimes resulted in products being miscounted before delivery.

The system provides a digital way to record products, automatically calculate quantities, and generate a printable PDF summary that can be used by the packing team to verify orders before they leave for delivery.

## The Problem

The existing process relied heavily on manual counting when preparing orders for delivery. This created the possibility of:

* Products being counted incorrectly
* Inconsistent order quantities
* Human error during the packing process
* Additional time being spent manually checking orders

## The Solution

The Order Tracking System automates the counting process by keeping track of product entries and automatically calculating the quantity of each product.

At the end of the process, the system generates a PDF summary that provides the packing team with a clear reference to compare against the physical products.

## Features

* Add products to an order
* Automatically track product quantities
* Case-insensitive product matching
* Automatically increment quantities when the same product is entered
* Remove products from the current order
* Clear the current order
* Maintain order state using PHP sessions
* Update the order without requiring a page reload
* Generate a downloadable PDF order summary
* Display the order summary in a structured table format

## Tech Stack

| Technology          | Purpose                                                                |
| ------------------- | ---------------------------------------------------------------------- |
| **PHP**             | Backend logic, product processing and session management               |
| **PHP Sessions**    | Maintains the current order state                                      |
| **JavaScript**      | Handles frontend interactions and asynchronous requests                |
| **AJAX / JSON**     | Communicates between the frontend and PHP backend without page reloads |
| **HTML**            | Structures the order entry interface                                   |
| **CSS**             | Provides styling for the user interface                                |
| **jsPDF**           | Generates PDF documents in the browser                                 |
| **jsPDF-AutoTable** | Formats the PDF order summary into a structured table                  |

## How It Works

1. A user enters product names into the order.
2. The frontend sends the product information to the PHP backend using an AJAX request.
3. PHP processes the product and stores the order information using sessions.
4. If the product already exists, the system automatically increments its quantity instead of creating a duplicate entry.
5. The current order is displayed to the user.
6. Once the order is complete, the user can generate and download a PDF summary.
7. The packing team uses the PDF as a reference when checking the physical order before delivery.

## Project Structure

The core functionality is contained within a single PHP file:

```text
order-tracking-system/
│
└── food_order.php
```

The application keeps the backend logic, frontend interface, JavaScript functionality, and PDF generation in one file. This keeps the application lightweight and allows it to be deployed easily on a basic PHP server.

## Why I Built This

This project was developed from a **real workplace problem** I observed while working as an administrative assistant.

I noticed that workers packing orders were relying on manual counts, which created opportunities for products to be miscounted. Rather than treating this as only an administrative issue, I identified an opportunity to use my software development skills to create a practical solution.

I built the Order Tracking System independently to automate the counting process and provide the packing team with a reliable reference when preparing deliveries.

This project was particularly important to me because it was the **first project I pushed to GitHub** and demonstrated how I could take a problem I encountered in a real working environment and turn it into a functional software solution.

## Skills Demonstrated

* Problem identification and solution design
* PHP development
* JavaScript development
* Session management
* AJAX and JSON communication
* Frontend development with HTML and CSS
* PDF generation
* Debugging and testing
* Translating a real-world business requirement into a software solution
* Git and GitHub version control

## Running the Project Locally

### Prerequisites

You will need:

* PHP
* A local PHP server such as **AMPPS**, **XAMPP**, or a similar environment
* A modern web browser

### Installation

1. Clone this repository:

```bash
git clone https://github.com/AGenge19/order-tracking-system
```

2. Place the project folder inside your local PHP server's web root.

For example:

```text
AMPPS/www/
```

or:

```text
XAMPP/htdocs/
```

3. Start your local PHP server.

4. Open the following file in your browser:

```text
food_order.php
```

5. Enter product names into the order.

6. Submit the order and generate the PDF summary.

## Future Improvements

Potential future improvements could include:

* Adding customer/order information
* Adding user authentication
* Storing completed orders in a database
* Adding order history
* Adding search and filtering
* Improving the user interface for mobile devices
* Adding different order statuses
* Deploying the application to a cloud-hosted environment

## Author

**Angela Genge**

Systems Development Graduate | Aspiring Cloud Engineer

This project represents my interest in using software development and cloud technologies to solve practical business problems.
