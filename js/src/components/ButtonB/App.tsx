import { useState } from '@wordpress/element'
import './index.scss'

export const App = () => {
	const [count, setCount] = useState(60)

	const add = () => setCount(prev => prev + 1)

	return <a className="aaa bbb" onClick={add}>A: {count}</a>
}
