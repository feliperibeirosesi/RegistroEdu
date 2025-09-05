//express é um framework que serve para facilitar rotas web

const express = require("express");

//serve como um bloqueio para somente quem eu queira que acesse
const cors = require("cors");

//serve para gerenciar variáveis de ambiente (em resumo é um .env)
const dotenv = require("dotenv");

//serve para conectar o BDD com o NODE
const mysql = require("mysql2/promise");

//serve para criptografar as senhas
const bcrypt = require("bcrypt");

const jwt = require("jsonwebtoken");

const PORT = 3001
const app = express();

dotenv.config();
app.use(cors());
app.use(express.json());

const pool = mysql.createPool({
  host: process.env.DB_HOST,
  user: process.env.DB_USER,
  password: process.env.DB_PASS,
  database: process.env.DB_NAME,

})

//middlewate
/*await.axios.get("http://localhost:3001/auth/profile"), {
header: { authorization: `Bearer ${token}`},
})
*/
function autenticarToken(req, res, next) {
  const authHeader = req.headers["authorization"];
  const token = authHeader && authHeader.split(" ")[1];

  if (!token) {
    return res.status(401).json({ error: "Token não fornecido!" })
  }

  jwt.verify(token, process.env.JWT_SECRET, (error, user) => {
    if (error) {
      return res.status(403).json({ error: "Token inválido!" })
    }

    req.user = user;
    console.log(req.user)
    next();
  })
}

app.post("/auth/register", async (req, res) => {
  try {
    const { nome, email, senha } = req.body

    if (!nome || !email || !senha) {
      return res.status(400).json({ error: "Preencha todos os campos!" });

    }

    const [rows] = await pool.query("SELECT id FROM users WHERE email = ?", [email]);
    if (rows.length > 0) {
      return res.status(400).json({ error: "Email já cadastrado!" })
    }

    const senha_hash = await bcrypt.hash(senha, 10);

    await pool.query("INSERT INTO users (nome, email, senha) VALUES (?, ?, ?)",
      [nome, email, senha_hash]
    );

    res.status(201).json({ message: "Usuário cadastrado com sucesso!" });

  } catch (error) {
    console.log(error)
    res.status(500).json({ error: "Erro ao registar usuário!" })
  }
});

app.post("/auth/login", async (req, res) => {
  try {
    const { email, senha } = req.body;

    const [rows] = await pool.query("SELECT * FROM users WHERE email = ?", [email])
    if (rows.length === 0) {
      return res.status(400).json({ error: "Usuário nao encontrado!" })
    }

    //aqui define o usuario.senha (que vem do banco)
    const usuario = rows[0];

    console.log(usuario)

    const senhaValida = await bcrypt.compare(senha, usuario.Senha)
    if (!senhaValida) {
      return res.status(401).json({ error: "Senha incorreta" });
    }

    const token = jwt.sign(
      { id: usuario.ID, email: usuario.Email },
      process.env.JWT_SECRET,
      { expiresIn: "1h" }
    )

    console.log(token)
    res.json({ message: "Login bem sucedido!", token })

  } catch (error) {
    console.log(error);
    res.status(500).json({ error: "Erro ao fazer login!" })
  }
})

app.get("/auth/profile", autenticarToken, async (req, res) => {
  try {

    const [rows] = await pool.query("SELECT * FROM users WHERE id = ?", [req.user.id]);


    if (rows.length === 0) {
      return res.status(404).json({ error: "Usuário não encontrado." })
    }

    res.json({ user: rows[0] })

  } catch (error) {
    console.log(error);
    res.status(500).json({ error: "Erro ao buscar dados do usuário!" })
  }
})

app.put("/auth/update", autenticarToken, async (req, res) => {
  try {
    const { nome, email } = req.body;

    if (!nome && !email) {
      return res.status(400).json({ error: "Informe nome ou email para atualizar" });
    }

    await pool.query(
      "UPDATE users SET nome = ?, email = ? WHERE id = ?", [nome, email, req.user.ID]
    )

    res.json({ message: "Dados atualizados com sucesso!" })

  } catch (error) {
    console.log(error);
    res.status(500).json({ error: "Erro ao buscar dados do usuário!" })
  }
})

async function conexaoBd() {
  try {
    const conn = await pool.getConnection();
    console.log("Conexão com MYSQL bem sucedida!");
    conn.release();
  } catch (error) {
    console.log(`Error: ${error}`)
  }
}
conexaoBd()

//iniciando o servidor!
app.listen(PORT, () => {
  console.log(`Servidorrodando na porta${PORT}!`)
});
