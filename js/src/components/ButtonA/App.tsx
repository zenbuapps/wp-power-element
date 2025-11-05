import { useState } from '@wordpress/element'
import styles from './index.module.css'

export const App = () => {
	const [count, setCount] = useState(0)

	const add = () => setCount(prev => prev + 1)

	return <p className={styles.button} onClick={add}>A: {count}</p>
}
