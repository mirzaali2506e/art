-- ============================================================
--  BeadCraft Store - Sample Data (optional)
--  Run AFTER importing schema.sql
--  Usage:  mysql -u root -p < seed.sql
-- ============================================================

USE beadcraft_store;

-- ----------------------------------------------------------
--  Categories
-- ----------------------------------------------------------
INSERT INTO categories (id, name, slug, description, image, sort_order, is_featured) VALUES
(1,  'Alphabets',                'alphabets',                'Letter beads for personalized bracelets',           NULL, 1, 1),
(2,  'Bracelet Kit Deals',       'bracelet-kit-deals',       'Complete kits with everything you need',            NULL, 2, 1),
(3,  'Crystal Beads 4mm',        'crystal-beads-4mm',        'Sparkling 4mm crystal glass beads',                NULL, 3, 1),
(4,  'Tulips Glass Flowers',     'tulips-glass-flowers',      'Delicate glass flower beads',                     NULL, 4, 1),
(5,  'Acrylic Charms',           'acrylic-charms',           'Colorful acrylic charm collection',                NULL, 5, 1),
(6,  'Charms',                   'charms',                   'Metal and enamel charms for bracelets',            NULL, 6, 1),
(7,  'Permanent Bracelet Chain', 'permanent-bracelet-chain',  'Stainless steel permanent bracelet chains',       NULL, 7, 0),
(8,  'Stainless Steel Chains',   'stainless-steel-chains',   'Durable stainless steel chain links',              NULL, 8, 0),
(9,  'Jewelry Accessories',      'jewelry-accessories',      'Clasps, jump rings, and findings',                 NULL, 9, 0),
(10, 'Crack Glass Beads 8mm',    'crack-glass-beads-8mm',    'Cracked glass effect beads, 8mm',                  NULL, 10, 1),
(11, 'Clay Beads',               'clay-beads',               'Handmade polymer clay beads',                      NULL, 11, 0),
(12, 'Pearl Designs',            'pearl-designs',            'Elegant pearl bead collection',                    NULL, 12, 0),
(13, 'Tools',                    'tools',                    'Pliers, cutters, and craft tools',                 NULL, 13, 0),
(14, 'Silk Yarn',               'silk-yarn',                'Premium silk yarn for bag making',                  NULL, 14, 0),
(15, 'Wool Yarn',                'wool-yarn',                'Soft wool yarn for crochet projects',               NULL, 15, 0),
(16, 'Packaging Materials',      'packaging-materials',      'Boxes, bags, and packaging supplies',              NULL, 16, 0);

-- ----------------------------------------------------------
--  Products
-- ----------------------------------------------------------
INSERT INTO products (category_id, name, slug, description, price, sale_price, stock, image, is_featured, is_active) VALUES
-- Alphabets
(1, 'Alphabet Beads A-Z White',         'alphabet-beads-white',         'White acrylic alphabet beads, 6mm. Full A-Z set.', 250, 199, 150, NULL, 1, 1),
(1, 'Alphabet Beads A-Z Black',          'alphabet-beads-black',         'Black acrylic alphabet beads with white letters, 6mm.', 280, 0, 100, NULL, 0, 1),
(1, 'Alphabet Beads A-Z Pastel',         'alphabet-beads-pastel',        'Pastel colored alphabet beads, mixed set.', 320, 250, 80, NULL, 1, 1),
(1, 'Alphabet Beads Heart Mix',          'alphabet-beads-heart-mix',     'Alphabet beads with heart accents, mixed colors.', 300, 0, 60, NULL, 0, 1),

-- Bracelet Kit Deals
(2, 'Starter Bracelet Kit',             'starter-bracelet-kit',        'Everything you need: beads, string, clasps, and tools.', 1500, 1200, 40, NULL, 1, 1),
(2, 'Deluxe Bracelet Kit',              'deluxe-bracelet-kit',         'Premium kit with 500+ beads, charms, and accessories.', 3000, 2500, 25, NULL, 1, 1),
(2, 'Friendship Bracelet Kit',           'friendship-bracelet-kit',     'Make 10 friendship bracelets with this complete kit.', 1200, 0, 50, NULL, 0, 1),
(2, 'Kids Bracelet Making Kit',           'kids-bracelet-kit',           'Safe and fun kit designed for children ages 6+.', 900, 750, 70, NULL, 0, 1),

-- Crystal Beads 4mm
(3, 'Crystal Beads 4mm Clear',          'crystal-beads-4mm-clear',     'Clear crystal glass beads, 4mm. 100 pieces.', 200, 0, 200, NULL, 1, 1),
(3, 'Crystal Beads 4mm Mixed',           'crystal-beads-4mm-mixed',     'Mixed color crystal beads, 4mm. 100 pieces.', 220, 180, 150, NULL, 1, 1),
(3, 'Crystal Beads 4mm AB Coated',       'crystal-beads-4mm-ab',         'Aurora Borealis coated crystal beads, 4mm.', 280, 0, 90, NULL, 0, 1),
(3, 'Crystal Beads 4mm Gold Line',       'crystal-beads-4mm-gold',      'Crystal beads with gold line accent, 4mm.', 300, 250, 60, NULL, 0, 1),

-- Tulips Glass Flowers
(4, 'Tulip Glass Flowers Pink',         'tulip-glass-pink',            'Pink glass tulip flower beads. Set of 20.', 350, 0, 80, NULL, 1, 1),
(4, 'Tulip Glass Flowers Purple',       'tulip-glass-purple',          'Purple glass tulip flower beads. Set of 20.', 350, 0, 60, NULL, 0, 1),
(4, 'Tulip Glass Flowers Mixed',         'tulip-glass-mixed',           'Mixed color glass tulip beads. Set of 30.', 480, 400, 45, NULL, 1, 1),

-- Acrylic Charms
(5, 'Acrylic Charms Animal Mix',         'acrylic-charms-animals',      'Cute animal acrylic charms. 20 pieces mixed.', 300, 0, 120, NULL, 1, 1),
(5, 'Acrylic Charms Fruit Mix',          'acrylic-charms-fruit',        'Fruit shaped acrylic charms. 20 pieces.', 300, 250, 90, NULL, 0, 1),
(5, 'Acrylic Charms Heart Set',          'acrylic-charms-hearts',       'Heart shaped acrylic charms in 10 colors.', 280, 0, 100, NULL, 0, 1),

-- Charms
(6, 'Metal Charms Gold Mix',             'metal-charms-gold',           'Gold tone metal charms, 15 piece mix.', 500, 0, 70, NULL, 1, 1),
(6, 'Metal Charms Silver Mix',           'metal-charms-silver',         'Silver tone metal charms, 15 piece mix.', 500, 420, 65, NULL, 0, 1),
(6, 'Enamel Charms Colorful',             'enamel-charms',               'Colorful enamel charms, 12 piece mix.', 600, 0, 50, NULL, 1, 1),

-- Permanent Bracelet Chain
(7, 'Permanent Bracelet Chain Gold',     'permanent-chain-gold',        '18K gold plated permanent bracelet chain.', 800, 0, 30, NULL, 0, 1),
(7, 'Permanent Bracelet Chain Silver',   'permanent-chain-silver',      '925 silver permanent bracelet chain.', 900, 750, 25, NULL, 0, 1),

-- Stainless Steel Chains
(8, 'Stainless Steel Chain 2mm',         'steel-chain-2mm',             '2mm stainless steel chain. Per meter.', 200, 0, 100, NULL, 0, 1),
(8, 'Stainless Steel Chain 3mm',         'steel-chain-3mm',             '3mm stainless steel chain. Per meter.', 250, 0, 80, NULL, 0, 1),

-- Jewelry Accessories
(9, 'Lobster Clasps Silver 50pc',        'lobster-clasps-silver',       'Silver lobster clasps, 50 pieces.', 150, 0, 200, NULL, 0, 1),
(9, 'Jump Rings Gold 200pc',             'jump-rings-gold',             'Gold jump rings, 200 pieces assorted sizes.', 200, 180, 150, NULL, 0, 1),
(9, 'Crimp Beads Silver 300pc',          'crimp-beads-silver',          'Silver crimp beads, 300 pieces.', 120, 0, 250, NULL, 0, 1),

-- Crack Glass Beads 8mm
(10, 'Crack Glass Beads 8mm Blue',       'crack-glass-blue',            'Blue cracked glass beads, 8mm. 50 pieces.', 250, 0, 120, NULL, 1, 1),
(10, 'Crack Glass Beads 8mm Green',      'crack-glass-green',           'Green cracked glass beads, 8mm. 50 pieces.', 250, 200, 100, NULL, 0, 1),
(10, 'Crack Glass Beads 8mm Mixed',      'crack-glass-mixed',           'Mixed color cracked glass beads, 8mm. 50 pieces.', 280, 0, 90, NULL, 1, 1),

-- Clay Beads
(11, 'Clay Beads Pastel Mix 100pc',     'clay-beads-pastel',           'Handmade pastel clay beads, 100 pieces.', 400, 0, 60, NULL, 0, 1),
(11, 'Clay Beads Earth Tones 100pc',    'clay-beads-earth',            'Earth tone clay beads, 100 pieces.', 400, 350, 40, NULL, 0, 1),

-- Pearl Designs
(12, 'Pearl Beads White 6mm 50pc',       'pearl-beads-white',           'White pearl beads, 6mm. 50 pieces.', 300, 0, 80, NULL, 0, 1),
(12, 'Pearl Beads Gold 8mm 30pc',        'pearl-beads-gold',            'Gold pearl beads, 8mm. 30 pieces.', 450, 0, 50, NULL, 0, 1),

-- Tools
(13, 'Jewelry Pliers Set 3pc',           'pliers-set',                 '3-piece pliers set: round, flat, and cutters.', 800, 0, 35, NULL, 0, 1),
(13, 'Beading Mat Large',               'beading-mat',                'Non-slip beading mat, 30x40cm.', 350, 0, 60, NULL, 0, 1),

-- Silk Yarn
(14, 'Silk Yarn Red 100g',              'silk-yarn-red',              'Red silk yarn, 100g skein.', 500, 0, 40, NULL, 0, 1),
(14, 'Silk Yarn Blue 100g',             'silk-yarn-blue',             'Blue silk yarn, 100g skein.', 500, 420, 30, NULL, 0, 1),

-- Wool Yarn
(15, 'Wool Yarn White 200g',            'wool-yarn-white',            'White wool yarn, 200g skein.', 400, 0, 50, NULL, 0, 1),
(15, 'Wool Yarn Rainbow Mix 200g',      'wool-yarn-rainbow',          'Rainbow mixed wool yarn, 200g skein.', 550, 0, 35, NULL, 0, 1),

-- Packaging
(16, 'Gift Boxes Small 20pc',           'gift-boxes-small',           'Small gift boxes for jewelry, 20 pieces.', 600, 0, 100, NULL, 0, 1),
(16, 'Organza Bags 50pc',               'organza-bags',               'Organza pouch bags, 50 pieces assorted colors.', 400, 350, 80, NULL, 0, 1);

-- ----------------------------------------------------------
--  Sample Reviews
-- ----------------------------------------------------------
INSERT INTO reviews (product_id, name, rating, comment, is_approved) VALUES
(1, 'Ayesha K.', 5, 'Amazing quality beads! The letters are clear and perfect for making name bracelets. Will order again!', 1),
(5, 'Fatima R.', 5, 'The starter kit has everything a beginner needs. Great value for money. Highly recommend!', 1),
(2, 'Sana M.', 4, 'Good quality kit but I wish it had more color variety. Still worth the price.', 1),
(10, 'Zainab A.', 5, 'These crystal beads are so sparkly and beautiful. Perfect for my jewelry business.', 1),
(6, 'Hira S.', 5, 'Fast delivery and excellent product quality. The charms are adorable!', 1),
(17, 'Maria J.', 4, 'Love the animal charms! My kids enjoy making bracelets with these. Good quality acrylic.', 1),
(25, 'Nida F.', 5, 'Best crack glass beads I have found in Pakistan. The colors are stunning.', 1),
(9, 'Rabia H.', 5, 'Great selection of beads. The mixed pack gives so many options for my projects.', 1);
