# Beautylatory - Comprehensive Style Guide

## Table of Contents
1. [Project Structure & Technology Stack](#1-project-structure--technology-stack)
2. [Color System](#2-color-system)
3. [Typography System](#3-typography-system)
4. [Spacing System](#4-spacing-system)
5. [Component Styling Patterns](#5-component-styling-patterns)
6. [Tailwind Configuration](#6-tailwind-configuration)
7. [CSS & Animations](#7-css--animations)
8. [Shadows & Elevation](#8-shadows--elevation)
9. [Border & Radius Patterns](#9-border--radius-patterns)
10. [Opacity & Transparency](#10-opacity--transparency)
11. [Backdrop Filters](#11-backdrop-filters)
12. [Responsive Design Breakpoints](#12-responsive-design-breakpoints)
13. [Interactive States](#13-interactive-states)
14. [Special Effects & Patterns](#14-special-effects--patterns)
15. [Layout Patterns](#15-layout-patterns)
16. [Typography & Text Styles](#16-typography--text-styles)
17. [Component Examples](#17-component-examples)
18. [Design Tokens Summary](#18-design-tokens-summary)
19. [Accessibility Features](#19-accessibility-features)
20. [Performance Optimizations](#20-performance-optimizations)

---

## 1. Project Structure & Technology Stack

### Framework & Tools
- **Framework**: Laravel 12 with PHP 8.4
- **Template Engine**: Blade (PHP templating)
- **Build Tool**: Vite 7 with Laravel Vite Plugin
- **CSS Framework**: Tailwind CSS v4 (CSS-first with @theme configuration)
- **JavaScript Framework**: Alpine.js v3.15 (lightweight reactivity)
- **Database**: SQLite

### Key Project Files
- `bootstrap/app.php` - Application configuration (middleware, routing)
- `routes/web.php` - Web route definitions
- `resources/views/` - Blade templates (layouts, pages, components)
- `resources/css/app.css` - Tailwind CSS 4 configuration with @theme
- `resources/js/app.js` - JavaScript entry point with Alpine.js
- `app/Http/Controllers/` - Request handlers
- `app/Models/` - Eloquent models (Product, Category, Article, etc.)

### Project Name
**Beautylatory** - Modern High-Tech Skincare E-commerce Platform (Laravel-based)

---

## 2. Color System

### Primary Tailwind Color Palette

#### Dark/Neutral Colors
```
gray-900: '#1A1A1A'    // Deep Charcoal (Primary text, dark backgrounds)
gray-50: '#fafafa'      // Off-white (Primary background)
gray-600: '(Default Tailwind)'  // Secondary text
gray-500: '(Default Tailwind)'  // Tertiary text
gray-400: '(Default Tailwind)'  // Light text
```

#### Rose/Pink Colors (Primary Accent)
```
rose-500: '#B76E79'    // Rose Gold (Primary accent, buttons, hover states)
rose-400: '#D49CA5'    // Lighter Rose (Gradients, text overlays)
rose-100: '(Derived)'  // Rose tint backgrounds
rose-50: '#FCE7E9'     // Lightest Rose (Selection background)
rose-200: '(Tailwind default)' // Card shadows
rose-600: '(For darker gradients)'
```

#### Nude/Beige Colors (Cosmetic Feel)
```
nude-100: '#F5E6E0'    // Cosmetic Nude (Warm beige backgrounds)
nude-200: '#EAD0C5'    // Slightly darker nude (Gradient stops)
```

#### Cyan/Tech Colors (AI Section Accent)
```
cyan-tech: '#00FFFF'   // Bright Tech Cyan (AI section accent, tech elements)
cyan-500: '#06b6d4'    // Standard Tailwind Cyan
cyan-400: '(Gradient usage)'  // Lighter cyan
cyan-900: '(Dark variant for shadows)'
```

#### Supporting Colors
```
white: '#FFFFFF'                // Cards, light elements
black: '#000000'                // Pure black (rare)
blue-50: '(Light blue)'         // Gradient background fills
purple-400: '(Gradient accent)' // Used in cyan-to-purple gradients
red-500: '(Status indicator)'   // Error/alert colors
yellow-500: '(Status indicator)'// Warning colors
green-500: '(Status indicator)' // Success colors
emerald-500: '(Success)'        // Shipping text, positive states
```

### Color Usage Patterns

| Element | Classes | Purpose |
|---------|---------|---------|
| Primary Text | `text-gray-900` | Main body copy, headings |
| Secondary Text | `text-gray-600`, `text-gray-500` | Descriptions, labels |
| Tertiary Text | `text-gray-400` | Subtle text, metadata |
| Accent Interactive | `text-rose-500` | Links, hover states, badges |
| Dark Backgrounds | `bg-gray-900` | Dark sections, overlays |
| Light Backgrounds | `bg-gray-50`, `bg-white` | Default page background, cards |
| Tech Accents | `text-cyan-tech` | AI Analysis section, tech features |
| Hover Effects | `hover:text-rose-500` | Interactive state feedback |

---

## 3. Typography System

### Font Families

#### Primary Fonts
```css
sans: ['Inter', 'sans-serif']              // Body text, UI, labels
display: ['Playfair Display', 'serif']     // Headlines, titles, luxury feel
```

### Font Weights

#### Inter Font Weights
- `font-light` (300): Body descriptions, light text
- `font-normal` (400): Default body text
- `font-medium` (500): Emphasis, medium importance
- `font-semibold` (600): Strong emphasis, section labels
- `font-bold` (700): Labels, buttons, strong text

#### Playfair Display Font Weights
- `font-normal` (400): Regular headlines
- `font-medium` (500): Most common for hero/section headings
- `font-semibold` (600): Emphasis, bold product names
- `font-bold` (700): Maximum emphasis headings
- Italic variants: For stylistic emphasis in gradients

### Font Size & Weight Usage

#### Hierarchical Text Styles

| Usage | CSS Classes | Size | Weight | Line Height | Letter Spacing |
|-------|-------------|------|--------|-------------|-----------------|
| **Hero H1** | `text-5xl md:text-7xl lg:text-8xl font-display font-medium` | 48px - 96px | 500 | 1.1 | Normal |
| **Section H2** | `text-4xl md:text-5xl font-display font-medium` | 36px - 48px | 500 | 1.2 | Normal |
| **Subheading H3** | `text-2xl font-display font-medium` | 24px | 500 | 1.2 | Normal |
| **Product H4** | `text-xl font-display` | 20px | 400 | Normal | Normal |
| **Body Text** | `text-base text-gray-600 font-light` | 16px | 300 | 1.5 | Normal |
| **Body Secondary** | `text-lg text-gray-600 font-light` | 18px | 300 | 1.625 | Normal |
| **Button Labels** | `text-xs font-bold tracking-widest uppercase` | 12px | 700 | Normal | 0.1em |
| **Micro Text** | `text-[10px] font-bold tracking-[0.2em]` | 10px | 700 | Normal | 0.2em |
| **Small Text** | `text-sm font-bold` | 14px | 700 | Normal | Normal |

### Text Style Modifiers

#### Italic & Emphasis
```typescript
italic                   // Apply italic style (used in gradient headings like "Radiance", "Diagnostics")
not-italic              // Remove italic style
```

#### Gradient Text
```typescript
text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-rose-600
// Creates text with gradient fill effect
// Commonly used: rose gradients, cyan-to-purple gradients
```

#### Text Transformation
```typescript
uppercase               // ALL CAPS (labels, buttons)
capitalize             // Capitalize First Letter
normal                 // Normal case (default)
```

#### Letter & Line Spacing
```typescript
tracking-normal        // 0px letter spacing (default)
tracking-wide          // 0.025em spacing
tracking-wider         // 0.05em spacing
tracking-widest        // 0.1em spacing (for labels)
tracking-[0.2em]       // Custom 0.2em (micro labels)
tracking-[0.1em]       // Custom 0.1em spacing

leading-none           // 1 line height (very tight)
leading-tight          // 1.25 line height
leading-snug           // 1.375 line height
leading-normal         // 1.5 line height (default)
leading-relaxed        // 1.625 line height
leading-loose          // 2 line height (very spacious)
leading-[1.1]          // Custom 1.1 (hero headings)
leading-[1.2]          // Custom 1.2 (section headings)
```

---

## 4. Spacing System

### Tailwind Standard Spacing Scale

| Value | Pixels | Usage |
|-------|--------|-------|
| `px` | 1px | Borders, dividers |
| `2` | 8px | Tiny gaps, micro spacing |
| `3` | 12px | Icon gaps, small components |
| `4` | 16px | Default padding, standard gaps |
| `6` | 24px | Medium padding, regular gaps |
| `8` | 32px | Large padding, section gaps |
| `10` | 40px | Large gaps, button padding |
| `12` | 48px | Section gaps, large components |
| `16` | 64px | Large section padding |
| `20` | 80px | XL section spacing |
| `24` | 96px | XXL spacing, section padding |
| `32` | 128px | Hero section padding |

### Common Spacing Patterns

#### Section Padding
```typescript
py-24              // 96px top/bottom padding (standard section)
py-32              // 128px top/bottom padding (larger sections)
px-6 md:px-8       // 24px mobile, 32px desktop horizontal padding
pt-32              // Page top padding (fixed header offset)
```

#### Component Spacing
```typescript
gap-3              // 12px gaps (icon + text)
gap-4              // 16px gaps (small lists)
gap-6              // 24px gaps (columns, rows)
gap-8              // 32px gaps (medium layout)
gap-12             // 48px gaps (large layout)
gap-16             // 64px gaps (major sections)
gap-20             // 80px gaps (hero section)

mb-6, mb-8         // Margin bottom (element spacing)
pb-6, pb-8         // Padding bottom (internal spacing)
space-y-4          // 16px vertical gaps between children
space-y-8          // 32px vertical gaps between children
```

#### Container & Layout
```typescript
container mx-auto px-6 md:px-8    // Standard layout container with padding
max-w-6xl mx-auto                 // Maximum width with centering
w-full                            // Full width elements
h-auto                            // Auto height
```

---

## 5. Component Styling Patterns

### Button Styles

#### Primary Dark Button
```html
<button class="bg-gray-900 text-white px-10 py-4 rounded-full
               text-xs font-bold tracking-widest uppercase
               hover:bg-rose-500 hover:shadow-lg hover:shadow-rose-500/20
               transition-all duration-300">
  Button Text
</button>
```
**Features:**
- Dark background with white text
- Large rounded corners (pill shape)
- All-caps label with wide letter spacing
- Rose-500 color on hover
- Subtle rose-tinted shadow on hover
- Smooth 300ms transition

#### Glass Panel Button
```html
<button class="glass-panel text-gray-900 px-10 py-4 rounded-full
               text-xs font-bold tracking-widest uppercase
               hover:bg-white transition-all duration-300">
  Button Text
</button>
```
**Features:**
- Frosted glass appearance (rgba with backdrop blur)
- Dark text on light glass
- Transitions to solid white on hover
- Maintains pill shape and letter spacing

#### Secondary Outlined Button
```html
<button class="border border-gray-200 text-gray-900 px-12 py-4 rounded-full
               text-xs font-bold tracking-[0.2em] uppercase
               hover:bg-gray-900 hover:text-white transition-all duration-300">
  Button Text
</button>
```
**Features:**
- Transparent with thin gray border
- Inverts colors on hover
- Extra wide tracking on text
- Bold weight for emphasis

#### Icon Button
```html
<button class="p-2 hover:bg-rose-50 rounded-full transition-colors">
  <svg class="w-5 h-5" />
</button>
```
**Features:**
- Circular hit area
- Subtle rose background on hover
- No border, minimal styling
- Perfect for navigation/action icons

#### Minimal Link Button
```html
<a class="text-xs font-bold tracking-widest text-gray-900
          border-b border-gray-300 pb-2
          hover:border-rose-500 hover:text-rose-500 transition-colors">
  Link Text
</a>
```
**Features:**
- Underline border effect
- Rises on hover with color change
- Minimal, elegant appearance
- 300ms color transition

### Card/Panel Styles

#### Glass Panel (Light)
```css
.glass-panel {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
}
```
**Characteristics:**
- Semi-transparent white (70%)
- 12px blur for frosted effect
- Subtle white border for definition
- Soft shadow for elevation
- Used for floating content, overlays, panels

#### Glass Panel Dark
```css
.glass-panel-dark {
  background: rgba(26, 26, 26, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}
```
**Characteristics:**
- Semi-transparent dark (80%)
- Same blur effect
- Very subtle border (10% white)
- Used for dark overlays, dark sections

#### Tailwind Glass Apply
```html
<div class="p-6 rounded-3xl backdrop-blur-md bg-white/80 border border-white">
  Content
</div>
```
**Tailwind approach** to glass effect without custom CSS.

#### Product Card
```html
<div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-6
            glass-panel p-3 transition-all duration-300
            hover:shadow-xl hover:shadow-rose-100/50">
  <img src="product.jpg" class="w-full h-full object-cover rounded-2xl" />
</div>
```
**Features:**
- Vertical aspect ratio (3:4 or 4:5)
- Glass panel wrapper
- Rounded image inside with padding
- Enhanced shadow on hover
- Smooth 300ms shadow transition

### Image Containers

#### Standard Rounded Images
```typescript
rounded-3xl        // 24px radius (product images, standard cards)
rounded-[2.5rem]   // 40px radius (large hero images)
rounded-[3rem]     // 48px radius (very large product detail)
rounded-full       // 9999px (circular avatars, icon containers)
```

#### Aspect Ratio Containers
```typescript
aspect-[3/4]       // Vertical product 3:4 ratio
aspect-[4/5]       // Taller product 4:5 ratio
aspect-square      // 1:1 ratio (editorial images)
aspect-[16/10]     // Horizontal article thumbnails
aspect-[21/9]      // Wide hero/article header
```

#### Image Styling
```html
<img class="w-full h-full object-cover rounded-3xl"
     src="image.jpg" alt="Description" />
```
- `object-cover`: Maintains aspect ratio, fills container
- Paired with parent `rounded-3xl` for consistent radius
- Full width/height fill of parent container

---

## 6. Tailwind CSS v4 Configuration

### CSS-First Configuration (No tailwind.config.js)

Tailwind CSS v4 uses a CSS-first approach. All configuration is done in `resources/css/app.css`:

#### Main CSS Entry Point
```css
@import "tailwindcss";

@theme {
  --font-sans: 'Inter', 'system-ui', sans-serif;
  --font-display: 'Playfair Display', serif;

  /* Custom Colors */
  --color-gray-900: #1A1A1A;
  --color-gray-50: #fafafa;

  --color-rose-500: #B76E79;
  --color-rose-400: #D49CA5;
  --color-rose-50: #FCE7E9;

  --color-nude-100: #F5E6E0;
  --color-nude-200: #EAD0C5;

  --color-cyan-tech: #00FFFF;

  /* Spacing (inherited from Tailwind default 4px scale) */
  /* No overrides needed, uses default 4px unit system */
}

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

#### Font Family Configuration
The fonts are defined in the `@theme` block above:
- `--font-sans`: Inter for body text and UI
- `--font-display`: Playfair Display for headings and luxury elements

#### Color Palette
All colors defined in `@theme` section:
- **Gray scale**: gray-900, gray-50, and Tailwind defaults for 100-600
- **Rose palette**: Primary accent color at #B76E79
- **Nude tones**: Warm beige for luxury feel
- **Cyan tech**: For AI and tech-focused sections

#### Key Differences from v3
- ❌ NO `tailwind.config.js` file
- ❌ NO `@tailwind` directives (old v3 syntax)
- ✅ Use `@import "tailwindcss"` (v4 syntax)
- ✅ Use `@theme` for configuration (v4 syntax)
- ✅ Use `@source` for content scanning (v4 syntax)
- ✅ Use `bg-white/80` for opacity (NOT `bg-opacity-80`)

---

## 7. CSS & Animations

### Custom CSS Styles

#### Body & Global Styles
```css
body {
  background-color: #fafafa;      /* Light gray background */
  color: #1A1A1A;                  /* Dark text color */
  font-family: 'Inter', sans-serif; /* Default body font */
}

html {
  scroll-behavior: smooth;         /* Smooth scroll on anchor links */
}
```

#### Glass Panel Effect
```css
.glass-panel {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);   /* Safari support */
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
}

.glass-panel-dark {
  background: rgba(26, 26, 26, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}
```

#### Scrollbar Hiding
```css
.hide-scrollbar::-webkit-scrollbar {
  display: none;                    /* Hide webkit scrollbars */
}

.hide-scrollbar {
  -ms-overflow-style: none;        /* IE and Edge */
  scrollbar-width: none;           /* Firefox */
}
```

### Animations & Transitions

#### Custom Keyframe Animations
```css
@keyframes scan-line {
  0% {
    top: 0%;
    opacity: 0;
  }
  10% {
    opacity: 1;
  }
  90% {
    opacity: 1;
  }
  100% {
    top: 100%;
    opacity: 0;
  }
}

.animate-scan {
  animation: scan-line 2s linear infinite;
}
```
**Usage:** AI scanning effect, technical UI elements

#### Built-in Tailwind Animations

| Animation | Duration | Easing | Usage |
|-----------|----------|--------|-------|
| `animate-pulse` | 2s | cubic-bezier | Pulsing dots, status indicators, attention-grabbing elements |
| `animate-scan` | 2s | linear infinite | Scanning line effect in AI section |
| `animate-[spin_10s_linear_infinite]` | 10s | linear | Dashed circular border rotation |
| `animate-bounce` | 1s | cubic-bezier | Bouncing elements (if used) |
| `transition-transform` | 300-700ms | ease-out | Hover scale/translate effects |
| `transition-all` | 300-500ms | ease-out | Button state changes, multi-property animations |
| `transition-colors` | 300ms | ease-out | Text/background color changes |
| `transition-shadow` | 300ms | ease-out | Shadow intensity changes |
| `transition-opacity` | 300ms | ease-out | Fade in/out effects |

#### Duration Classes
```typescript
duration-300        // 300ms (quick interactions)
duration-500        // 500ms (medium animations)
duration-700        // 700ms (image zoom on hover)
duration-[2s]       // 2s (smooth image zoom)
duration-[1.5s]     // 1.5s (article image hover)
duration-[3s]       // 3s (slow animations)
```

#### Timing Functions
```typescript
ease-in             // Slow start
ease-out            // Slow end (most common)
ease-in-out         // Slow start and end
ease-linear         // Constant speed (used in rotations)
```

### Common Hover Effects

#### Image Zoom
```html
<div class="group">
  <img class="group-hover:scale-105 transition-transform duration-700" />
</div>
```
**Properties:**
- `scale-105`: 5% zoom
- `scale-110`: 10% zoom (more dramatic)
- `duration-700`: 700ms transition time
- **Use case:** Product images, article thumbnails

#### Color Transitions
```html
<button class="hover:text-rose-500 transition-colors">
  Text
</button>
```
**Properties:**
- `transition-colors`: Animate color changes only
- `duration-300`: 300ms by default
- **Use case:** Button text, link colors

#### Background Changes
```html
<button class="hover:bg-rose-500 transition-all duration-300">
  Button
</button>
```
**Properties:**
- `transition-all`: Animate all properties
- Smooth color shift on hover
- **Use case:** Button backgrounds, overlay effects

#### Shadow Effects
```html
<div class="hover:shadow-lg hover:shadow-rose-500/20">
  Card
</div>
```
**Properties:**
- `shadow-lg`: Larger shadow
- `shadow-rose-500/20`: Rose-tinted at 20% opacity
- **Use case:** Card elevation, depth feedback

#### Transform Effects
```html
<div class="group-hover:-translate-y-1 group-hover:translate-x-1 transition-transform">
  Element
</div>

<div class="group-hover:scale-110 transition-transform duration-300">
  Zoom Element
</div>
```
**Properties:**
- `-translate-y-1`: Move up by 4px
- `translate-x-1`: Move right by 4px
- `scale-110`: 10% larger
- **Use case:** Interactive cards, floating effects

#### Opacity & Reveal Animations
```html
<div class="opacity-0 translate-y-4 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-500 ease-out">
  Hidden Element
</div>
```
**Properties:**
- Starts invisible and moved down
- Reveals and moves up on hover
- Smooth 500ms animation
- **Use case:** Overlay buttons, hidden content

---

## 8. Shadows & Elevation

### Shadow Specifications

#### Standard Shadow Sizes
```typescript
shadow-sm          // Small shadow (subtle elevation)
shadow-md          // Medium shadow
shadow-lg          // Large shadow (most common for cards)
shadow-xl          // Extra large shadow
shadow-2xl         // 2xl shadow (strongest)
```

### Colored Shadows (Tinted Elevation)

#### Rose-Tinted Shadows
```typescript
shadow-rose-100        // Soft rose shadow (30% opacity)
shadow-rose-100/50     // Rose shadow at 50% opacity
shadow-rose-200/50     // Lighter rose shadow at 50%
shadow-rose-500/20     // Rose accent shadow at 20% (subtle)
shadow-rose-500/30     // Rose accent shadow at 30%
```
**Usage:** Product cards, feminine design elements, accent shadows

#### Gray-Tinted Shadows
```typescript
shadow-gray-100        // Subtle gray shadow (light)
shadow-gray-200        // Slightly darker gray shadow
shadow-gray-900/10     // Very subtle dark shadow (10%)
shadow-gray-900/20     // Subtle dark shadow (20%)
```
**Usage:** General cards, neutral elements, depth

#### Cyan-Tinted Shadows
```typescript
shadow-cyan-900/20     // Tech cyan dark shadow at 20%
```
**Usage:** AI section containers, tech-focused elements

### Shadow Usage Patterns

| Element | Shadow Classes | Effect |
|---------|----------------|--------|
| Product Cards | `shadow-2xl shadow-rose-200` | Soft elevation with rose tint |
| Hover Cards | `hover:shadow-xl hover:shadow-rose-100/50` | Enhanced shadow on interaction |
| Editorial Images | `shadow-2xl shadow-gray-200` | Professional, neutral elevation |
| AI Container | `shadow-2xl shadow-cyan-900/20` | Tech aesthetic with subtle tint |
| Article Thumbnails | `shadow-lg shadow-gray-100` | Minimal elevation |
| Button Hover | `hover:shadow-lg hover:shadow-rose-500/20` | Accent shadow on interaction |

### Elevation Hierarchy

```
No Shadow: Interactive elements, minimal depth
shadow-sm: Small components, subtle depth
shadow-md: Medium components, clear elevation
shadow-lg: Cards, primary elements
shadow-xl: Large cards, hover states
shadow-2xl: Maximum elevation, hero elements
```

---

## 9. Border & Radius Patterns

### Border Radius Values

#### Standard Radius Sizes
```typescript
rounded-lg          // 8px (small components, inputs)
rounded-xl          // 12px (buttons, medium components)
rounded-2xl         // 16px (product tags, small cards)
rounded-3xl         // 24px (image containers, cards) - MOST COMMON
rounded-[2.5rem]    // 40px (large image containers, hero sections)
rounded-[3rem]      // 48px (largest containers, maximum smoothness)
rounded-full        // 9999px (perfect circles, pill buttons, avatars)
```

#### Radius Usage
| Radius | Size (px) | Usage |
|--------|-----------|-------|
| `rounded-lg` | 8 | Input fields, small buttons |
| `rounded-xl` | 12 | Medium buttons, tags |
| `rounded-2xl` | 16 | Small cards, badges |
| `rounded-3xl` | 24 | **Product cards, standard images** |
| `rounded-[2.5rem]` | 40 | Large sections, hero images |
| `rounded-[3rem]` | 48 | Maximum radius (luxury feel) |
| `rounded-full` | ∞ | Buttons, circular elements |

### Border Styles

#### Border Width
```typescript
border           // 1px solid border
border-0         // No border
border-2         // 2px border
border-4         // 4px border (used for left border emphasis)
```

#### Border Direction
```typescript
border           // All sides
border-t         // Top only
border-b         // Bottom only
border-l         // Left only
border-r         // Right only
border-l-4       // Left side with 4px width
```

#### Border Colors
```typescript
border-white         // Clean white border
border-white/50      // Semi-transparent white (50%)
border-white/20      // Very transparent white (20%)
border-white/10      // Barely visible white (10%)
border-white/5       // Almost invisible white (5%)
border-gray-100      // Light gray border
border-gray-200      // Medium-light gray
border-gray-300      // Medium gray
border-rose-200      // Soft rose border
border-rose-500      // Accent rose border
border-cyan-tech     // Tech cyan border
border-cyan-tech/30  // Semi-transparent cyan (30%)
border-purple-400    // Purple accent border
border-purple-400/30 // Semi-transparent purple (30%)
```

#### Border Styles
```typescript
border-solid     // Solid line (default)
border-dashed    // Dashed line (for decorative borders)
border-dotted    // Dotted line
```

---

## 10. Opacity & Transparency

### Background Opacity Values

#### White Background Opacity
```typescript
bg-white         // Fully opaque white
bg-white/90      // 90% opacity (nearly opaque)
bg-white/80      // 80% opacity (glass panels)
bg-white/50      // 50% opacity (semi-transparent)
bg-white/5       // 5% opacity (barely visible tint)
```

#### Gray Background Opacity
```typescript
bg-gray-900      // Fully opaque dark
bg-gray-900/20   // 20% opacity dark overlay
bg-gray-900/10   // 10% opacity very subtle overlay
bg-gray-900/0    // 0% opacity (transparent)
```

#### Accent Color Opacity
```typescript
bg-rose-500/10   // 10% opacity rose (subtle tint)
bg-rose-100/40   // 40% opacity light rose (gradient background)
bg-cyan-500/10   // 10% opacity cyan (tech tint)
```

#### Gradient Opacity
```typescript
bg-rose-100/40      // Used in gradient stops
bg-nude-100/40      // Warm beige gradient
bg-blue-50/30       // Cool blue gradient
```

#### Glass Panel Opacity
```typescript
rgba(255, 255, 255, 0.7)   // 70% opacity white (light glass)
rgba(255, 255, 255, 0.8)   // 80% opacity white
rgba(26, 26, 26, 0.8)      // 80% opacity dark (dark glass)
```

### Text & Border Opacity

#### Text Opacity
```typescript
text-gray-400/60     // 60% opacity gray text
text-cyan-tech/60    // 60% opacity cyan text
```

#### Border Opacity (detailed in Border section)
```typescript
border-white/50      // 50% opacity white border
border-white/20      // 20% opacity white border
border-white/10      // 10% opacity white border
border-white/5       // 5% opacity white border
border-cyan-tech/30  // 30% opacity cyan border
border-purple-400/30 // 30% opacity purple border
```

---

## 11. Backdrop Filters

### Glassmorphic Effects

#### Blur Effects
```typescript
backdrop-blur-md    // Medium blur (12px)
backdrop-blur-xl    // Extra large blur (24px)
backdrop-blur-[120px] // Ultra heavy blur (120px) - used for ambient backgrounds
```

#### Blur Implementation
```css
.glass-panel {
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);    /* Safari support */
}
```

#### Common Blur Patterns
```html
<!-- Light glass panel -->
<div class="backdrop-blur-md bg-white/80 border border-white">
  Content
</div>

<!-- Heavy blur for ambient effect -->
<div class="absolute backdrop-blur-[120px] bg-rose-500/10">
  Decorative background
</div>

<!-- Dark glass panel -->
<div class="glass-panel-dark backdrop-blur-md">
  Dark content
</div>
```

### Blur Performance Notes
- **GPU accelerated** on modern browsers
- Hardware acceleration via `transform3d`
- Consider reducing blur on lower-end devices
- Backdrop blur is more performant than box-shadow filters

---

## 12. Responsive Design Breakpoints

### Tailwind Breakpoints

#### Standard Breakpoints
```typescript
// Mobile-first approach (default, no prefix)
(no prefix)          // Always applied to all screen sizes

// Medium screens (768px and up)
md:                  // Tablet and desktop

// Large screens (1024px and up)
lg:                  // Desktop
```

### Responsive Usage Patterns

#### Text Scaling
```typescript
text-5xl md:text-7xl lg:text-8xl
// Mobile: 48px, Tablet: 56px, Desktop: 64px
```

#### Padding/Spacing
```typescript
px-6 md:px-8          // 24px mobile, 32px desktop
py-24 md:py-32 lg:py-40
gap-8 lg:gap-20       // 32px mobile, 80px desktop
```

#### Grid Layouts
```typescript
grid-cols-1 sm:grid-cols-2 lg:grid-cols-4
// Mobile: 1 column, Tablet: 2 columns, Desktop: 4 columns

grid-cols-2 md:grid-cols-5
// Mobile: 2 columns, Desktop: 5 columns
```

#### Display Toggle
```typescript
hidden md:flex        // Hidden on mobile, visible on tablet+
md:block              // Visible on tablet+
lg:hidden             // Visible on mobile/tablet, hidden on desktop
```

#### Element Ordering
```typescript
order-2 lg:order-1    // Reorder content on desktop
flex-col md:flex-row  // Stack mobile, side-by-side desktop
```

#### Width Constraints
```typescript
w-full md:w-1/2       // Full width mobile, half width desktop
max-w-md md:max-w-2xl // Smaller on mobile, larger on desktop
```

---

## 13. Interactive States

### Hover States

#### Background Color Hover
```typescript
hover:bg-rose-50      // Subtle rose background
hover:text-rose-500   // Text color change
hover:bg-rose-500     // Strong background change to rose
hover:bg-white        // Change to white
hover:border-rose-500 // Border color change
```

#### Shadow & Elevation Hover
```typescript
hover:shadow-lg       // Increase shadow
hover:shadow-xl       // Larger shadow
hover:shadow-rose-500/20   // Add colored shadow
hover:shadow-rose-100/50   // Rose-tinted shadow
```

#### Transform Hover
```typescript
hover:scale-105       // 5% zoom (subtle)
hover:scale-110       // 10% zoom (more dramatic)
hover:-translate-y-1  // Move up 4px
hover:translate-x-1   // Move right 4px
```

#### Opacity Hover
```typescript
hover:opacity-80      // Slight fade
hover:opacity-100     // Fully opaque (from faded state)
```

### Group Hover (Parent Effects)

#### Group Hover Patterns
```html
<div class="group">
  <img class="group-hover:scale-105 transition-transform duration-700" />
  <button class="opacity-0 group-hover:opacity-100" />
  <p class="group-hover:text-rose-500 transition-colors" />
</div>
```

#### Group Hover Effects
```typescript
group-hover:opacity-100    // Reveal hidden content
group-hover:translate-y-0  // Slide in elements
group-hover:text-rose-500  // Change text color
group-hover:bg-white       // Change background
group-hover:shadow-xl      // Enhance shadow
group-hover:scale-110      // Zoom on parent hover
```

### Focus States

#### Input Focus
```typescript
focus:outline-none           // Remove default outline
focus:border-rose-500        // Highlight border on focus
focus:ring-0                 // Remove focus ring
```

#### Keyboard Navigation
```typescript
focus-visible:outline-2      // Visible outline for keyboard nav
focus-visible:outline-rose-500
```

### Disabled States

```typescript
disabled:opacity-50         // Fade disabled elements
disabled:cursor-not-allowed // Change cursor
disabled:bg-gray-200        // Gray out disabled buttons
```

### Placeholder States

```typescript
placeholder:text-gray-400   // Light gray placeholder text
placeholder:text-gray-600   // Darker placeholder
```

---

## 14. Special Effects & Patterns

### Gradient Patterns

#### Text Gradients
```html
<h1 class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-rose-600">
  Gradient Text
</h1>
```
**Common Gradients:**
- `from-rose-400 to-rose-600`: Rose gold gradient
- `from-cyan-400 to-purple-400`: Tech gradient

#### Background Gradients
```html
<div class="bg-gradient-to-b from-transparent to-gray-900/20">
  Gradient background
</div>
```

#### Radial Gradients
```css
background-image: radial-gradient(var(--tw-gradient-stops));
```
Used for spotlight effects, centered highlights.

### Blur Effects for Ambient Design

#### Heavy Blur (120px)
```html
<div class="absolute blur-[120px] bg-rose-500/10">
  Ambient background
</div>
```
**Usage:**
- Large decorative elements
- Background atmosphere
- Not interactive, purely visual

#### Standard Blur
```typescript
blur-3xl         // 64px blur (heavy)
blur-2xl         // 40px blur (moderate)
blur-xl          // 24px blur
```

### Mix Blend Modes

```typescript
mix-blend-multiply     // Darkens (good for overlays)
mix-blend-screen       // Lightens (good for glows)
mix-blend-overlay      // Combines both
mix-blend-luminosity   // Blends on luminosity channel
```

### Pointer Events

```typescript
pointer-events-none   // Element can't be interacted with
pointer-events-auto   // Normal interaction (default)
```

### Z-Index Layering

```typescript
z-0             // Layer 0
z-10            // Layer 10
z-20            // Layer 20
z-30            // Layer 30
z-40            // Layer 40
z-50            // Layer 50 (highest common)

// Usage:
z-0  md:z-10   // Different z-index at different breakpoints
```

### Position Patterns

```typescript
absolute                  // Absolute positioning
absolute inset-0         // Fill entire parent
absolute -top-[20%]      // Positioned above parent
absolute -left-[10%]     // Positioned left of parent
absolute top-1/2 left-0  // Vertically centered, left aligned
absolute bottom-10 right-10  // Bottom right corner
```

---

## 15. Layout Patterns

### Grid Systems

#### Product Grid (4-column desktop)
```html
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
  <div>Product</div>
  <!-- ... -->
</div>
```

#### Editorial Grid (2-column with image + text)
```html
<div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
  <img />
  <div>Content</div>
</div>
```

#### Feature Grid (5-column on desktop)
```html
<div class="grid grid-cols-2 md:grid-cols-5 gap-12 md:gap-8">
  <div>Feature</div>
  <!-- ... -->
</div>
```

#### Why Us Section (5-column grid)
```html
<div class="grid grid-cols-2 md:grid-cols-5 gap-12 md:gap-8">
  <div>Benefit</div>
  <!-- ... -->
</div>
```

#### Article Grid (3-column on desktop)
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-16">
  <article>Article card</article>
  <!-- ... -->
</div>
```

#### Product Detail (12-column layout)
```html
<div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20">
  <div class="lg:col-span-6">Image</div>
  <div class="lg:col-span-6">Details</div>
</div>
```

### Flexbox Patterns

#### Header Navigation
```html
<header class="flex items-center justify-between">
  <logo />
  <nav />
  <cart />
</header>
```

#### Vertical Stack with Spacing
```html
<div class="flex flex-col gap-8">
  <h1>Title</h1>
  <p>Content</p>
  <button>Action</button>
</div>
```

#### Center Everything
```html
<div class="flex items-center justify-center h-screen">
  Centered content
</div>
```

#### Icon + Text Alignment
```html
<div class="flex items-center gap-2">
  <Icon />
  <span>Label</span>
</div>
```

#### Space Between Distribution
```html
<div class="flex justify-between">
  <div>Left</div>
  <div>Right</div>
</div>
```

#### Responsive Direction
```html
<div class="flex flex-col md:flex-row gap-8">
  <div>Mobile: stacked, Desktop: side-by-side</div>
</div>
```

#### Grow/Shrink
```html
<div class="flex">
  <div class="flex-1">Takes remaining space</div>
  <div class="flex-shrink-0">Doesn't shrink</div>
</div>
```

### Container & Centering

#### Standard Layout Container
```html
<div class="container mx-auto px-6 md:px-8">
  Page content
</div>
```

#### Max Width Constraints
```typescript
max-w-md        // 335px (small)
max-w-lg        // 512px (medium)
max-w-2xl       // 672px (large)
max-w-3xl       // 768px (extra large)
max-w-4xl       // 896px (XXL)
max-w-6xl       // 1152px (3XL - most common)
```

#### Centering Methods
```html
<!-- Margin auto centering -->
<div class="mx-auto max-w-6xl">Centered content</div>

<!-- Flexbox centering -->
<div class="flex items-center justify-center">Centered</div>

<!-- Grid centering -->
<div class="grid place-items-center">Centered</div>

<!-- Text alignment -->
<div class="text-center">Centered text</div>
```

---

## 16. Typography & Text Styles

### Text Alignment

```typescript
text-left       // Left aligned (default)
text-center     // Center aligned
text-right      // Right aligned
text-justify    // Justify (rarely used)
```

### Text Transform

```typescript
uppercase       // ALL CAPS (labels, buttons, overlines)
lowercase       // all lowercase
capitalize      // Capitalize First Letter
normal          // Normal case (default)
```

### Text Decoration

```typescript
underline       // Apply underline
no-underline    // Remove underline (default)
line-through    // Strike through
overline        // Overline (rarely used)
```

### Text Truncation

```typescript
truncate        // Single line truncation with ellipsis
line-clamp-2    // Limit to 2 lines
line-clamp-3    // Limit to 3 lines
line-clamp-4    // Limit to 4 lines
```

### Letter Spacing (Tracking)

```typescript
tracking-normal     // 0px (default)
tracking-tight      // -0.025em
tracking-tighter    // -0.05em
tracking-wide       // 0.025em
tracking-wider      // 0.05em
tracking-widest     // 0.1em (labels)
tracking-[0.2em]    // Custom 0.2em (micro labels)
tracking-[0.1em]    // Custom 0.1em
```

### Line Height

```typescript
leading-none        // 1 (very tight)
leading-tight       // 1.25
leading-snug        // 1.375
leading-normal      // 1.5 (default)
leading-relaxed     // 1.625
leading-loose       // 2 (very spacious)
leading-[1.1]       // Custom 1.1 (hero headings)
leading-[1.2]       // Custom 1.2 (section headings)
```

### Word Breaking

```typescript
break-normal    // Normal word breaking
break-words     // Break long words
break-all       // Break at any character
```

### Whitespace

```typescript
whitespace-normal    // Normal whitespace
whitespace-nowrap    // Prevent wrapping
whitespace-pre       // Preserve whitespace
whitespace-pre-wrap  // Preserve and wrap
```

---

## 17. Component Examples

### Header Component (Blade)

**Key Characteristics:**
- Fixed positioning with Alpine.js scroll detection
- Glass panel effect appears on scroll
- Logo in display font (Playfair Display)
- Navigation links with hover effects (rose-500 color)
- Search input with glass effect styling
- Icon buttons (cart, menu) with circular hover backgrounds
- Mobile menu with Alpine.js toggle
- Smooth transitions on all interactions (300ms)

**Styling Approach:**
```html
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
        x-data="{ isScrolled: false, isMobileMenuOpen: false }">
  <div class="container mx-auto px-6 md:px-8 flex items-center justify-between"
       :class="isScrolled ? 'glass-panel py-3' : 'bg-transparent py-6'">
    <h1 class="text-2xl font-display font-semibold">Beautylatory</h1>

    <nav class="hidden md:flex gap-8">
      <a href="{{ route('products.index') }}" class="text-gray-900 hover:text-rose-500 transition-colors">
        Products
      </a>
    </nav>

    <button class="relative p-2 hover:bg-rose-50 rounded-full transition-colors">
      <svg class="w-6 h-6"></svg>
    </button>
  </div>
</header>
```

**Alpine.js Features:**
- `x-data="{ isScrolled: false, isMobileMenuOpen: false }"` - Manages scroll and menu state
- `:class="isScrolled ? 'glass-panel py-3' : 'bg-transparent py-6'"` - Conditional glass effect
- `@keydown.escape.window="isMobileMenuOpen = false"` - Close menu with Escape key

### Hero Component (Blade)

**Key Characteristics:**
- Full viewport height (min-h-screen)
- Grid layout with text on left, image on right
- Large display font heading (text-8xl) with gradient text option
- Glass panel badge with pulsing indicator dot
- Two action buttons (dark primary + glass secondary)
- Large product image with rounded corners (rounded-3xl) and shadow
- Smooth image zoom (scale-105) on hover

**Styling Approach:**
```blade
<section class="min-h-screen grid grid-cols-1 lg:grid-cols-2 gap-12 items-center py-32 px-6 md:px-8">
  <div class="space-y-8">
    <div class="inline-block glass-panel px-6 py-3 rounded-full">
      <div class="flex items-center gap-3">
        <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
        <span class="text-sm font-semibold">New Collection</span>
      </div>
    </div>
    <h1 class="text-8xl font-display font-medium leading-[1.1]">
      <span class="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-rose-600">
        Radiance
      </span>
    </h1>
    <div class="flex gap-6">
      <a href="{{ route('products.index') }}" class="bg-gray-900 text-white px-10 py-4 rounded-full font-bold hover:bg-rose-500 transition-all duration-300">
        Shop Now
      </a>
      <button class="glass-panel text-gray-900 px-10 py-4 rounded-full font-bold hover:bg-white transition-all duration-300">
        Explore
      </button>
    </div>
  </div>
  <div class="group cursor-pointer">
    <img src="{{ asset('images/hero.webp') }}"
         alt="Radiance Product"
         class="w-full rounded-3xl shadow-2xl group-hover:scale-105 transition-transform duration-700" />
  </div>
</section>
```

**Laravel Features:**
- `{{ route('products.index') }}` - Named route for navigation
- `{{ asset('images/hero.webp') }}` - Public asset path helper
- Blade directives work alongside Tailwind CSS classes

### Product Card (Blade)

**Key Characteristics:**
- Vertical aspect ratio (aspect-[3/4] or aspect-[4/5])
- Glass panel wrapper with rounded corners (rounded-3xl)
- Overlay badge positioned on image
- Discount percentage badge in top-left
- Product title with hover color change to rose-500
- Price displayed with strikethrough for discounts
- "View Details" button reveals on hover
- Shadow enhancement on hover (shadow-xl shadow-rose-100/50)

**Styling Approach:**
```blade
<div class="group">
  <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden mb-6 glass-panel p-3
              transition-all duration-300 hover:shadow-2xl hover:shadow-rose-200">
    <img src="{{ asset($product->image) }}"
         alt="{{ $product->name }}"
         loading="lazy"
         class="w-full h-full object-cover rounded-[1.5rem] group-hover:scale-110 transition-transform duration-500" />

    @if($product->discount_price)
      <span class="absolute top-4 left-4 bg-rose-500 text-white px-3 py-1 rounded-full text-xs font-bold">
        {{ round(((($product->price - $product->discount_price) / $product->price) * 100)) }}% OFF
      </span>
    @endif

    <a href="{{ route('products.show', $product->id) }}"
       class="absolute bottom-0 left-0 right-0 bg-gray-900/80 text-white py-3 text-center font-semibold
              opacity-0 group-hover:opacity-100 translate-y-4 group-hover:translate-y-0
              transition-all duration-300">
      View Details
    </a>
  </div>

  <h3 class="text-lg font-display text-gray-900 group-hover:text-rose-500 transition-colors mb-2 line-clamp-2">
    {{ $product->name }}
  </h3>

  <div class="flex items-center gap-2 mb-4">
    @if($product->discount_price)
      <span class="text-rose-500 font-semibold">${{ number_format($product->discount_price, 2) }}</span>
      <span class="text-gray-400 line-through text-sm">${{ number_format($product->price, 2) }}</span>
    @else
      <span class="text-rose-500 font-semibold">${{ number_format($product->price, 2) }}</span>
    @endif
  </div>

  <a href="{{ route('products.show', $product->id) }}"
     class="w-full glass-panel px-4 py-3 rounded-full font-bold text-center
            hover:bg-white transition-all duration-300 block">
    Add to Cart
  </a>
</div>
```

**Laravel Features:**
- `{{ asset($product->image) }}` - Load product image from storage
- `{{ $product->name }}` - Display product name with escaping
- `line-clamp-2` - Limit product name to 2 lines
- Conditional discount badge: `@if($product->discount_price)`
- `number_format()` for formatted pricing

### Section Header Pattern

**Key Characteristics:**
- Centered max-width text introduction
- Consistent spacing (py-24 or py-32)
- Large section heading in display font
- Optional subtitle or description
- Center-aligned text styling

**Styling Approach:**
```html
<section class="py-24">
  <div class="container mx-auto px-6 md:px-8 max-w-3xl text-center space-y-6 mb-16">
    <h2 class="text-5xl font-display font-medium">Section Title</h2>
    <p class="text-lg text-gray-600 font-light">
      Section description goes here
    </p>
  </div>
  <!-- Content grid below -->
</section>
```

### Editorial/About Section

**Key Characteristics:**
- Two-column grid layout (image + text)
- Large product/lifestyle image on one side
- Text content aligned with image
- Mixed typography (display font for heading, sans for body)
- Flexible ordering (reorder on desktop with lg:order-*)
- Consistent spacing between content blocks

**Styling Approach:**
```html
<section class="py-32">
  <div class="container mx-auto px-6 md:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 lg:gap-20 items-center">
      <div class="order-2 lg:order-1">
        <h2 class="text-5xl font-display font-medium mb-6">Section Heading</h2>
        <p class="text-lg text-gray-600 font-light mb-8">Content description</p>
        <button>Learn More</button>
      </div>
      <div class="order-1 lg:order-2">
        <img class="w-full rounded-3xl shadow-2xl" />
      </div>
    </div>
  </div>
</section>
```

---

## 18. Design Tokens Summary

### Core Design Tokens

| Token | Value | Purpose | Tailwind Class |
|-------|-------|---------|-----------------|
| **Primary Accent** | Rose #B76E79 | Buttons, hover states, badges, active elements | `text-rose-500` `bg-rose-500` |
| **Primary Dark** | Gray-900 #1A1A1A | Main text, dark buttons, backgrounds | `text-gray-900` `bg-gray-900` |
| **Primary Light** | Gray-50 #fafafa | Main page background | `bg-gray-50` |
| **Secondary Text** | Gray-600/500 | Descriptions, muted text | `text-gray-600` `text-gray-500` |
| **Tertiary Text** | Gray-400 | Minimal text, metadata | `text-gray-400` |
| **Tech Accent** | Cyan #00FFFF | AI section, tech features | `text-cyan-tech` |
| **Body Font** | Inter | All body copy, UI text | `font-sans` |
| **Display Font** | Playfair Display | Headings, titles, brand | `font-display` |
| **Border Radius** | 24px-48px | Cards, images, modern feel | `rounded-3xl` `rounded-[3rem]` |
| **Shadow** | Soft (0 4px 30px rgba) | Elevation, depth | `shadow-lg` `shadow-2xl` |
| **Transition Duration** | 300-700ms | Interactive animations | `duration-300` `duration-700` |
| **Spacing Unit** | 4px (base) | Consistent spacing throughout | `4` (1 unit) |
| **Glass Blur** | 12px | Panel effects | `backdrop-blur-md` |

### Font Weight Hierarchy

| Weight | Inter | Playfair Display | Usage |
|--------|-------|------------------|-------|
| 300 | Light | - | Body descriptions |
| 400 | Normal | Normal | Default body, product names |
| 500 | Medium | Medium | **Most common headings** |
| 600 | Semibold | Semibold | Strong emphasis, labels |
| 700 | Bold | Bold | Maximum emphasis, buttons |

### Color Opacity Patterns

| Opacity | Usage | Example |
|---------|-------|---------|
| 0% | Transparent | `bg-gray-900/0` |
| 5% | Barely visible | `bg-white/5` `border-white/5` |
| 10% | Very subtle | `bg-gray-900/10` `shadow-gray-900/10` |
| 20% | Subtle | `shadow-rose-500/20` `border-white/20` |
| 30% | Semi-transparent | `border-cyan-tech/30` `border-purple-400/30` |
| 40% | Moderate transparency | `bg-rose-100/40` `bg-blue-50/40` |
| 50% | Half transparency | `bg-white/50` `shadow-rose-100/50` |
| 60% | Mostly visible | `text-gray-400/60` `text-cyan-tech/60` |
| 70% | Glass panels | `bg-white/70` |
| 80% | Mostly opaque | `bg-white/80` `bg-gray-900/80` |
| 90% | Nearly opaque | `bg-white/90` |
| 100% | Fully opaque | Default, no suffix |

---

## 19. Accessibility Features

### Text Selection Styling
```html
<body class="selection:bg-rose-200 selection:text-gray-900">
```
**Purpose:** Makes text selection visible with rose highlight and dark text for contrast.

### Focus States Implementation
- Focus outlines removed (`focus:outline-none`) for aesthetic consistency
- Focus indicated by **color changes** or **border changes**
- All interactive elements remain keyboard accessible
- Focus states provide clear visual feedback

### Proper Contrast Ratios
- Text on light backgrounds: Dark gray (gray-900)
- Text on dark backgrounds: White or light gray
- Links and interactive text: Rose-500 (sufficient contrast)
- Disabled states: Reduced opacity with gray-400/400

### Icon Accessibility
- Icons use CSS class colors matching text
- Group hover affects child icons consistently
- Proper contrast with backgrounds maintained
- Alternative text (aria-label) for icon buttons

### Semantic HTML
- Use of heading hierarchy (h1, h2, h3, etc.)
- Button elements for interactive controls
- Link elements for navigation
- Article elements for content sections

---

## 20. Performance Optimizations

### Image Loading Strategy

**Lazy Loading:**
- Picsum placeholder images used throughout
- Images load progressively as user scrolls
- `loading="lazy"` attribute on appropriate images

**Image Display:**
- `object-cover` for consistent image display
- Maintains aspect ratios within containers
- Prevents layout shift from varying dimensions

### Smooth Scrolling

**Scroll Behavior:**
- `scroll-smooth` class implied for anchor navigation
- Smooth scroll to section links (no jump)
- Hide scrollbar classes for custom scroll areas (`.hide-scrollbar`)

### Animation Performance

**Hardware Acceleration:**
- Use `transform` for animations (GPU-accelerated)
- Avoid animating `left`, `top`, `width`, `height`
- Scale, rotate, translate are optimal

**Backdrop Filter Performance:**
- GPU-friendly `backdrop-blur` implementation
- `-webkit-backdrop-filter` for Safari support
- Used strategically on non-animated elements

**Transition Optimization:**
- Only transition necessary properties
- `transition-transform` for movement
- `transition-colors` for color changes
- `transition-shadow` for shadow effects
- `transition-all` only when needed for multiple properties

### CSS File Size

**Tailwind CSS Strategy:**
- CDN implementation (production consideration: use build process)
- Utility-first approach reduces custom CSS
- Minimal custom CSS in `<style>` tags
- Reusable utility classes prevent duplication

### Resource Hints

**Recommended additions:**
```html
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://cdn.tailwindcss.com">
```

### Code Splitting

**Component-based Architecture:**
- React components load on demand
- Separate component files for modularity
- Vite provides automatic code splitting

---

## Quick Reference

### Most Common Classes

#### Spacing
```
py-24 py-32 px-6 md:px-8 gap-8 gap-16
```

#### Typography
```
text-5xl md:text-7xl lg:text-8xl (Hero)
text-4xl md:text-5xl (Section heading)
text-xl (Product name)
text-base text-gray-600 font-light (Body)
text-xs font-bold tracking-widest uppercase (Labels)
```

#### Colors
```
text-gray-900 text-gray-600 text-rose-500
bg-gray-900 bg-gray-50 bg-white
hover:text-rose-500 hover:bg-rose-500
```

#### Buttons
```
bg-gray-900 text-white px-10 py-4 rounded-full
text-xs font-bold tracking-widest uppercase
hover:bg-rose-500 transition-all duration-300
```

#### Cards/Panels
```
glass-panel rounded-3xl p-6 shadow-lg shadow-rose-200
```

#### Images
```
rounded-3xl object-cover aspect-[3/4]
group-hover:scale-105 transition-transform duration-700
```

#### Responsive
```
grid-cols-1 md:grid-cols-2 lg:grid-cols-4
text-base md:text-lg lg:text-xl
hidden md:block
```

---

## Conclusion

This comprehensive style guide documents the **Beautylatory** design system in detail. The system emphasizes:

- **Modern Luxury Aesthetic**: Rose gold accents, sophisticated typography, glass effects
- **High-Tech Feel**: Cyan accents, scanning animations, tech gradients
- **Responsive Design**: Mobile-first approach with seamless desktop experience
- **Performance**: GPU-accelerated animations, lazy loading, efficient CSS
- **Accessibility**: Proper contrast, keyboard navigation, semantic HTML
- **Consistency**: Reusable tokens, predictable spacing, unified component patterns

Use this guide to maintain design consistency across all implementation, updates, and new features.
