-- Dra. Samla Cristie - Database Initialization Script
-- This script is run automatically when the MySQL container starts

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Admin users
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_group` VARCHAR(50) DEFAULT 'geral',
    `setting_label` VARCHAR(200),
    `setting_type` VARCHAR(20) DEFAULT 'text',
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(50) DEFAULT 'fas fa-paw',
    `image` VARCHAR(255),
    `whatsapp_text` VARCHAR(500),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members
CREATE TABLE IF NOT EXISTS `team` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `role` VARCHAR(200),
    `description` TEXT,
    `image` VARCHAR(255),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `initials` VARCHAR(5),
    `color` VARCHAR(20) DEFAULT '#4285f4',
    `rating` DECIMAL(2,1) DEFAULT 5.0,
    `text` TEXT NOT NULL,
    `date_label` VARCHAR(50),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog articles
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(300) NOT NULL UNIQUE,
    `excerpt` TEXT,
    `content` LONGTEXT,
    `image` VARCHAR(255),
    `category` VARCHAR(100),
    `author` VARCHAR(200) DEFAULT 'Dra. Samla Cristie',
    `read_time` VARCHAR(20) DEFAULT '5 min',
    `featured` TINYINT(1) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FAQ items
CREATE TABLE IF NOT EXISTS `faq` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(100) NOT NULL,
    `category_icon` VARCHAR(50) DEFAULT 'fas fa-paw',
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Specialties
CREATE TABLE IF NOT EXISTS `specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-star',
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clients
CREATE TABLE IF NOT EXISTS `clients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `type` VARCHAR(100),
    `description` TEXT,
    `logo_icon` VARCHAR(50) DEFAULT 'fas fa-hospital',
    `logo_color` VARCHAR(20) DEFAULT '#2d5016',
    `location` VARCHAR(200),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- SAMPLE DATA - Dados de Exemplo
-- ============================================

-- Serviços
INSERT IGNORE INTO `services` (`title`, `description`, `icon`, `image`, `whatsapp_text`, `sort_order`, `active`) VALUES
('Consulta Clínica Geral', 'Atendimento veterinário completo com anamnese, avaliação física e diagnóstico. Indicado para rotina, vacinas e check-ups periódicos.', 'fas fa-stethoscope', '2.jpeg', 'gostaria de agendar uma consulta clínica geral', 1, 1),
('Medicina Equina', 'Especialização em clínica geral de equinos. Atendimento no estábulo com equipamentos portáteis para sua segurança e conforto.', 'fas fa-horse', '8.jpeg', 'preciso de um veterinário para meu cavalo', 2, 1),
('Pequenos Animais', 'Clínica geral para cães e gatos com diagnóstico por imagem, ultrassom e laboratorial. Vacinação, vermifugação e atendimentos preventivos.', 'fas fa-paw', '1.jpeg', 'tenho dúvidas sobre a saúde do meu pet', 3, 1),
('Animais Silvestres', 'Atendimento especializado para aves, répteis, pequenos mamíferos e outros silvestres. Reabilitação e cuidados específicos de cada espécie.', 'fas fa-eagle', '5.jpeg', 'tenho um animal silvestre que precisa de atendimento', 4, 1),
('Cirurgias Veterinárias', 'Procedimentos cirúrgicos de pequeno e médio porte com anestesia segura e monitoramento contínuo. Centro cirúrgico equipado com tecnologia moderna.', 'fas fa-syringe', '9.jpeg', 'meu animal precisa de cirurgia', 5, 1),
('Ultrassom e Diagnóstico', 'Ultrassom abdominal, torácico e obstétrico. Diagnóstico por imagem de alta qualidade para precisão em tratamentos.', 'fas fa-waveform-lines', '7.jpeg', 'gostaria de fazer um ultrassom', 6, 1);

-- Equipe
INSERT IGNORE INTO `team` (`name`, `role`, `description`, `image`, `sort_order`, `active`) VALUES
('Dra. Samla Cristie', 'Médica Veterinária - Proprietária', 'Formada pela Universidade Estadual de Goiás. Especialista em clínica geral com foco em medicina equina e animais de produção. Dedicada a levar medicina de qualidade ao campo.', '3.jpeg', 1, 1),
('Dr. Carlos Santos', 'Cirurgião Veterinário', 'Especialista em procedimentos cirúrgicos com 12 anos de experiência. Realiza desde pequenas intervenções até cirurgias complexas com segurança.', '10.jpeg', 2, 1),
('Dra. Marina Lima', 'Clínica de Pequenos Animais', 'Apaixonada por cães e gatos. Realiza diagnósticos avançados em ultrassom e oferece acompanhamento integral dos seus animaizinhos.', '11.jpeg', 3, 1),
('Tecnólogo Anderson', 'Auxiliar Técnico', 'Responsável por exames laboratoriais e preparação do centro cirúrgico. Garante esterilização e segurança em todos os procedimentos.', '12.jpeg', 4, 1);

-- Depoimentos de Clientes
INSERT IGNORE INTO `testimonials` (`name`, `initials`, `color`, `rating`, `text`, `date_label`, `sort_order`, `active`) VALUES
('Roberto Oliveira', 'RO', '#FF6B6B', 5, 'Excelente atendimento! A Dra. Samla é muito atenciosa e cuidadosa com o meu cavalo. Recomendo para todos os proprietários de equinos na região.', '4 semanas atrás', 1, 1),
('Juliana Costa', 'JC', '#4ECDC4', 5, 'Levei meu cachorro com cólica e a equipe foi rápida no diagnóstico. Procedimento seguro e meu pet chegou em casa recuperado e feliz.', '3 semanas atrás', 2, 1),
('Felipe Mendes', 'FM', '#95E1D3', 5, 'Profissionais de excelência! A Dra. Samla opera meus animais de trabalho há 3 anos. Confiança total no trabalho dela.', '2 semanas atrás', 3, 1),
('Patricia Silva', 'PS', '#F38181', 5, 'Meus gatos adoram quando vamos lá. O ambiente é tranquilo e a equipe muito carinhosa com os animais.', '1 semana atrás', 4, 1),
('Marcelo Rocha', 'MR', '#AA96DA', 5, 'Encontrei a veterinária que procurava! Competente, dedicada e com um olhar humanizado para com os animais. Muito obrigado!', 'Há 2 dias', 5, 1);

-- Artigos do Blog
INSERT IGNORE INTO `articles` (`title`, `slug`, `excerpt`, `content`, `image`, `category`, `read_time`, `active`) VALUES
('Cuidados Essenciais com o Cavalo no Verão', 'cuidados-cavalo-verao', 'O calor extremo do verão pode afetar a saúde dos equinos. Conheça as principais medidas para manter seu cavalo saudável durante essa estação.', '<h2>Hidratação é Fundamental</h2><p>Durante o verão, os cavalos perdem muito líquido através da transpiração. Oferça água fresca em abundância, preferencialmente em múltiplos bebedouros para evitar competição entre os animais.</p><h2>Proteção Solar</h2><p>Animais com pelagem clara são mais propensos a queimaduras solares. Use roupas protetoras ou oferça sombra durante os períodos mais quentes do dia. Aplicar protetor solar em áreas sensíveis é recomendado.</p><h2>Alimentação Adequada</h2><p>Reduza volumosos muito fibrosos e aumente alimentos com maior digestibilidade. A fenaçã picada é uma ótima opção para o verão.</p><h2>Exercício Moderado</h2><p>Evite exercícios intensos durante as horas mais quentes. Prefira treinar no início da manhã ou ao entardecer. Ofereça muita água após o exercício.</p>', '6.jpeg', 'Cuidados Equinos', '4 min', 1),
('Sinais de que Seu Cão Pode Estar Com Dor', 'sinais-cao-com-dor', 'Muitas vezes os cães escondem sua dor. Saiba quais são os sinais de alerta que não devem ser ignorados.', '<h2>Mudanças no Comportamento</h2><p>Um cachorro com dor frequentemente se torna mais isolado, agressivo ou ansioso. Procure por mudanças súbitas na personalidade do seu pet.</p><h2>Dificuldades de Locomoção</h2><p>Manqueira, relutância em pular ou subir escadas, e dificuldade para levantar são sinais claros de desconforto nas articulações ou músculos.</p><h2>Alterações no Apetite</h2><p>Perda de apetite é frequentemente um sinal de dor crônica. Se seu cão para de comer normalmente, investigar com um veterinário é essencial.</p><h2>Sinais Físicos</h2><p>Procure por inflamação, feridas, sensibilidade ao toque ou alterações na respiração. Esses sinais indicam que uma avaliação veterinária é necessária.</p>', '5.jpeg', 'Pequenos Animais', '5 min', 1),
('Vacinação em Gatos: Tudo o que Você Precisa Saber', 'vacinacao-gatos-guia', 'Entenda o calendário vacinal para gatos e por que cada vacina é importante para a prevenção de doenças.', '<h2>Vacinação Inicial</h2><p>Kittens devem receber as primeiras vacinas aos 6-8 semanas de idade. A série inicial inclui 3 doses com intervalo de 3-4 semanas entre elas.</p><h2>Vacinas Essenciais (Core Vaccines)</h2><p>A tríplice felina protege contra rinotraqueíte viral, calicivírus e panleucopenia. Esta é essencial para todos os gatos, independentemente do estilo de vida.</p><h2>Vacinas Não Essenciais</h2><p>A vacina contra leucemia felina (FeLV) e raiva são recomendadas dependendo do risco de exposição do seu gato.</p><h2>Revacinação</h2><p>Após a série inicial, seu gato precisa de reforços anuais. Algumas vacinas podem ser feitas a cada 3 anos conforme orientação do veterinário.</p>', '1.jpeg', 'Pequenos Animais', '4 min', 1),
('Como Identificar e Prevenir Problemas Bucais em Pets', 'saude-bucal-pets', 'A saúde bucal é muitas vezes negligenciada, mas é crucial para a qualidade de vida dos animais. Conheça os principais problemas e como previni-los.', '<h2>Sinais de Problema Bucal</h2><p>Halitose (mau hálito), dificuldade para comer, salivação excessiva e sangramento das gengivas são sinais de alerta.</p><h2>Limpeza Preventiva</h2><p>A escovação regular dos dentes é a melhor prevenção contra tártaro e doença periodontal. Comece cedo e faça uma rotina diária.</p><h2>Alimentos e Brinquedos Apropriados</h2><p>Alimentos secos adequados e brinquedos mastigáveis podem ajudar a manter os dentes limpos naturalmente.</p><h2>Avaliação Profissional</h2><p>Limpezas profissionais periódicas são recomendadas pela maioria dos veterinários para prevenir complicações sérias.</p>', '8.jpeg', 'Prevenção', '5 min', 1),
('Nutrição Adequada para Répteis e Aves', 'nutricao-repteis-aves', 'Cada espécie silvestre tem necessidades nutricionais específicas. Descubra como oferecer uma alimentação balanceada.', '<h2>Necessidades Diferentes por Espécie</h2><p>Não existe uma dieta única para todos os répteis e aves. Serpentes necessitam de presas, enquanto papagaios precisam de frutas, vegetais e sementes balanceadas.</p><h2>Suplementação Essencial</h2><p>Cálcio e vitamina D3 são críticos para a saúde óssea. Deficiências podem levar a problemas graves de desenvolvimento e fratura.</p><h2>Frequência de Alimentação</h2><p>A idade e espécie determinam com que frequência alimentar. Filhotes necessitam alimentação mais frequente que adultos.</p><h2>Variação e Qualidade</h2><p>Ofereça variedade de alimentos para garantir nutrientes balanceados. Sempre utilize alimentos/presas de qualidade e procedência confiável.</p>', '12.jpeg', 'Animais Silvestres', '6 min', 1);

-- FAQ
INSERT IGNORE INTO `faq` (`category`, `category_icon`, `question`, `answer`, `sort_order`, `active`) VALUES
('Geral', 'fas fa-question-circle', 'Qual o horário de atendimento?', 'Atendemos de segunda a sexta-feira das 8h às 18h, e aos sábados das 8h às 12h. Agendamentos podem ser feitos via WhatsApp ou telefone.', 1, 1),
('Geral', 'fas fa-question-circle', 'Fazem atendimento domiciliar?', 'Sim! Realizamos visitas para animais de produção e equinos nas propriedades rurais da região. O valor da visita é cobrado conforme a distância.', 2, 1),
('Equinos', 'fas fa-horse', 'Qual a frequência de vacinação para cavalos?', 'Recomendamos vacinação contra tétano anualmente. A vacinação contra influenza equina é recomendada a cada 6 meses. Consulte o veterinário para um plano personalizado.', 1, 1),
('Equinos', 'fas fa-horse', 'Como identificar cólica em equinos?', 'Sinais incluem agitação excessiva, rolamento frequente, recusa em comer, sudoração e postura anormal. A cólica é emergência e requer atendimento imediato.', 2, 1),
('Pequenos Animais', 'fas fa-paw', 'Com que idade posso castrar meu cão ou gato?', 'Geralmente recomendamos castração a partir dos 6 meses de idade. Em alguns casos, pode ser feito mais cedo. Consulte o veterinário para orientação específica.', 1, 1),
('Pequenos Animais', 'fas fa-paw', 'Qual é o calendário vacinal recomendado?', 'Filhotes recebem 3 doses de vacina polivalente com intervalo de 3-4 semanas. Reforço anual é necessário. Raiva é obrigatória por lei em alguns municípios.', 2, 1),
('Cirurgias', 'fas fa-syringe', 'Como preparar meu animal para cirurgia?', 'Jejum de 6-8 horas é obrigatório. Realizar exames pré-operatórios é recomendado. A equipe fornecerá instruções detalhadas na marcação.', 1, 1),
('Cirurgias', 'fas fa-syringe', 'Qual é o pós-operatório recomendado?', 'Repouso absoluto por 10-14 dias, cuidados com a ferida, medicações conforme prescrito e retorno para avaliar evolução. Os pontos são removidos em 10-14 dias.', 2, 1);

-- Especialidades
INSERT IGNORE INTO `specialties` (`name`, `icon`, `sort_order`, `active`) VALUES
('Clínica Geral', 'fas fa-stethoscope', 1, 1),
('Cirurgia Veterinária', 'fas fa-syringe', 2, 1),
('Medicina Equina', 'fas fa-horse', 3, 1),
('Animais Silvestres', 'fas fa-eagle', 4, 1);

-- Clientes
INSERT IGNORE INTO `clients` (`name`, `type`, `description`, `logo_icon`, `logo_color`, `location`, `sort_order`, `active`) VALUES
('Clínica Vera Cruz - Pet', 'Clínica Veterinária', 'Parceiros em atendimento a pequenos animais na zona urbana. Referência em terapias inovadoras.', 'fas fa-clinic-medical', '#FF6B6B', 'Goiânia, GO', 1, 1),
('Haras Santa Maria', 'Propriedade Rural', 'Grande propriedade especializada em criação de cavalos de raça. Confiaram na Dra. Samla há mais de 3 anos.', 'fas fa-horse', '#4ECDC4', 'Região Rural, GO', 2, 1),
('PetShop Animal House', 'Pet Shop', 'Parceria contínua para vacinação, vermifugação e orientações ao cliente. Excelente relacionamento comercial.', 'fas fa-shop', '#95E1D3', 'Goiânia, GO', 3, 1),
('Fazenda Três Irmãos', 'Propriedade Rural', 'Atendemos regularmente para sanidade de rebanho e atendimentos emergenciais. Muito satisfeitos com nossos serviços.', 'fas fa-barn', '#F38181', 'Interior, GO', 4, 1),
('Instituto de Fauna Silvestre', 'ONG Ambiental', 'Parceria em projetos de reabilitação e soltura de animais silvestres. Contribuindo com preservação ambiental.', 'fas fa-leaf', '#AA96DA', 'Goiânia, GO', 5, 1),
('Hospital Veterinário Saúde Animal', 'Hospital Veterinário', 'Referência em procedimentos cirúrgicos complexos. Realizamos interconsultas e encaminhamentos especializados.', 'fas fa-hospital', '#FFB6C1', 'Goiânia, GO', 6, 1);
