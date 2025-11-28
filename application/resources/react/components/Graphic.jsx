import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';
import faker from 'faker';

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

export const options = {
  responsive: true,
  plugins: {
    title: {
      display: false,
      text: 'Presença ao longo do ano',
    },
  },
};

const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August'];

export const data = {
  labels,
  datasets: [
    {
      label: 'Faltas',
      data: labels.map(() => faker.datatype.number({ min: 0, max: 1000 })),
      backgroundColor: 'rgba(255, 99, 132, 0.5)',
    },
    {
      label: 'Presença',
      data: labels.map(() => faker.datatype.number({ min: 0, max: 1000 })),
      backgroundColor: 'rgba(53, 162, 235, 0.5)',
    },
  ],
};

export default function Graphic() {
  return <Bar options={options} data={data} />;
}
